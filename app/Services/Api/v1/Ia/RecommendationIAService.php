<?php

namespace App\Services\Api\v1\Ia;

use App\Contracts\Api\v1\Ia\ProductIaInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecommendationIAService
{
    private ?string $aiApiUrl;
    private ?string $openAiKey;
    private string $openAiBaseUrl;
    private string $openAiModel;

    public function __construct(
        private ProductIaInterface $repository
    ) {
        // URL de tu API Python (desde .env)
        $this->aiApiUrl = config('integrations.ia.url_ia');
        $this->openAiKey = config('services.openai.api_key');
        $this->openAiBaseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->openAiModel = config('services.openai.model', 'gpt-3.5-turbo');
    }

    /**
     * Envía una consulta al sistema de IA para obtener recomendaciones.
     *
     * @param string $query Consulta del usuario ("necesito un mouse gaming")
     * @param string|null $conversationId ID de conversación (para seguimiento)
     * @return array Respuesta de la IA
     */
    public function recommend(string $query, ?string $conversationId = null): array
    {
        $localRecommendations = $this->buildLocalRecommendations($query);

        try {
            if ($this->openAiKey) {
                $response = Http::timeout(160)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->openAiKey}",
                        'Content-Type' => 'application/json',
                    ])
                    ->post("{$this->openAiBaseUrl}/chat/completions", [
                        'model' => $this->openAiModel,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Eres un asistente de ventas especializado en recomendacion de productos informaticos. Responde en espanol y SOLO puedes recomendar productos listados en el catalogo entregado. No inventes productos ni caracteristicas.',
                            ],
                            [
                                'role' => 'user',
                                'content' => $this->buildOpenAiPrompt($query, $localRecommendations),
                            ],
                        ],
                        'temperature' => 0.7,
                        'max_tokens' => 500,
                    ]);

                if ($response->failed()) {
                    Log::error('OpenAI API Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    throw new Exception('Error al comunicarse con OpenAI');
                }

                $responseData = $response->json();
                $message = data_get($responseData, 'choices.0.message.content', 'No se recibió respuesta de OpenAI.');

                return [
                    'type' => 'openai',
                    'message' => trim($message),
                    'products' => $localRecommendations,
                    'conversation_id' => $conversationId ?: Str::uuid()->toString(),
                    'question_count' => 1,
                ];
            }

            if ($this->aiApiUrl) {
                $response = Http::timeout(160)
                    ->post("{$this->aiApiUrl}/recommend", [
                        'query' => $query,
                        'conversation_id' => $conversationId,
                    ]);

                if ($response->failed()) {
                    Log::error('AI API Error', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    throw new Exception('Error al comunicarse con el sistema de IA');
                }

                $external = $response->json();

                return [
                    'type' => data_get($external, 'type', 'local'),
                    'message' => data_get($external, 'message', $this->buildLocalMessage($localRecommendations, $query)),
                    'products' => data_get($external, 'products') ?: $localRecommendations,
                    'conversation_id' => data_get($external, 'conversation_id', $conversationId ?: Str::uuid()->toString()),
                    'question_count' => data_get($external, 'question_count', 1),
                ];
            }

            return [
                'type' => 'local',
                'message' => $this->buildLocalMessage($localRecommendations, $query),
                'products' => $localRecommendations,
                'conversation_id' => $conversationId ?: Str::uuid()->toString(),
                'question_count' => 1,
            ];
        } catch (Exception $e) {
            Log::error('AI Recommendation Error', [
                'message' => $e->getMessage(),
                'query' => $query
            ]);

            return [
                'type' => 'local_fallback',
                'message' => $this->buildLocalMessage($localRecommendations, $query),
                'products' => $localRecommendations,
                'conversation_id' => $conversationId ?: Str::uuid()->toString(),
                'question_count' => 1,
            ];
        }
    }

    protected function buildLocalRecommendations(string $query, int $limit = 3): array
    {
        $tokens = $this->tokenizeQuery($query);
        $products = $this->repository->getAllForAI();

        $scored = $products->map(function ($product) use ($tokens) {
            $name = (string) $product->name;
            $model = (string) $product->model;
            $brand = (string) ($product->brand?->name ?? '');
            $category = (string) ($product->subcategory?->category?->name ?? '');
            $subcategory = (string) ($product->subcategory?->name ?? '');
            $description = (string) ($product->description ?? '');

            $fields = [
                'name' => Str::lower($name),
                'model' => Str::lower($model),
                'brand' => Str::lower($brand),
                'category' => Str::lower($category),
                'subcategory' => Str::lower($subcategory),
                'description' => Str::lower($description),
            ];

            $score = 0.0;
            $reasons = [];

            foreach ($tokens as $token) {
                if (str_contains($fields['name'], $token)) {
                    $score += 3.0;
                    $reasons[] = "coincide en nombre ({$token})";
                    continue;
                }

                if (str_contains($fields['brand'], $token) || str_contains($fields['model'], $token)) {
                    $score += 2.5;
                    $reasons[] = "coincide en marca/modelo ({$token})";
                    continue;
                }

                if (str_contains($fields['category'], $token) || str_contains($fields['subcategory'], $token)) {
                    $score += 2.0;
                    $reasons[] = "coincide en categoria ({$token})";
                    continue;
                }

                if (str_contains($fields['description'], $token)) {
                    $score += 1.2;
                    $reasons[] = "coincide en descripcion ({$token})";
                }
            }

            $specText = Str::lower($product->specifications->pluck('pivot.value')->implode(' '));
            foreach ($tokens as $token) {
                if (str_contains($specText, $token)) {
                    $score += 1.2;
                    $reasons[] = "coincide en especificaciones ({$token})";
                }
            }

            $variants = $product->variants->map(function ($variant) {
                return [
                    'id' => (int) $variant->id,
                    'sku' => (string) $variant->sku,
                    'price' => (float) $variant->selling_price,
                    'stock' => (int) $variant->branches->sum('pivot.stock'),
                    'features' => $variant->optionProductValues->map(function ($feature) {
                        return [
                            'option' => (string) ($feature->optionValue->option->name ?? ''),
                            'value' => (string) ($feature->optionValue->description ?? ''),
                            'type' => (string) ($feature->optionValue->option->type ?? ''),
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray();

            $specifications = $product->specifications->map(function ($spec) {
                return [
                    'name' => (string) $spec->name,
                    'value' => (string) ($spec->pivot->value ?? ''),
                ];
            })->values()->toArray();

            $normalized = min(99, (int) round($score * 12));

            return [
                'id' => (int) $product->id,
                'name' => $name,
                'model' => $model,
                'description' => $description,
                'brand' => $brand ?: 'Sin marca',
                'category' => $category ?: 'Sin categoria',
                'subcategory' => $subcategory ?: 'Sin subcategoria',
                'specifications' => $specifications,
                'variants' => $variants,
                'similarity_score' => round($score, 2),
                'match_score' => $normalized,
                'match_reason' => $this->buildMatchReason($reasons),
            ];
        });

        $best = $scored
            ->sortByDesc('match_score')
            ->filter(fn ($item) => $item['match_score'] > 0)
            ->take($limit)
            ->values();

        if ($best->isNotEmpty()) {
            return $best->all();
        }

        return $scored
            ->sortByDesc(fn ($item) => collect($item['variants'])->sum('stock'))
            ->take($limit)
            ->values()
            ->all();
    }

    protected function tokenizeQuery(string $query): array
    {
        $rawTokens = preg_split('/\\s+/u', Str::lower(trim($query))) ?: [];

        $stopWords = [
            'de', 'del', 'la', 'el', 'los', 'las', 'un', 'una', 'unos', 'unas',
            'para', 'por', 'con', 'sin', 'que', 'quiero', 'necesito', 'busco',
            'me', 'mi', 'y', 'o', 'en', 'es', 'al', 'a'
        ];

        return collect($rawTokens)
            ->map(fn ($token) => preg_replace('/[^\\p{L}\\p{N}]/u', '', (string) $token))
            ->filter(fn ($token) => filled($token) && mb_strlen($token) >= 2)
            ->reject(fn ($token) => in_array($token, $stopWords, true))
            ->values()
            ->all();
    }

    protected function buildMatchReason(array $reasons): string
    {
        if (empty($reasons)) {
            return 'Producto sugerido por disponibilidad en catalogo';
        }

        return 'Te lo sugiero porque ' . implode(', ', array_slice(array_unique($reasons), 0, 2)) . '.';
    }

    protected function buildLocalMessage(array $products, string $query): string
    {
        if (empty($products)) {
            return 'No encontre productos que coincidan con tu consulta. Puedes intentar con otra descripcion mas especifica.';
        }

        $first = $products[0];
        $price = collect($first['variants'] ?? [])->min('price');
        $priceText = is_numeric($price) ? ' desde S/ ' . number_format((float) $price, 2) : '';

        return sprintf(
            'Para "%s" te sugiero %s%s. Tambien te dejo mas opciones relacionadas para que compares.',
            $query,
            $first['name'] ?? 'este producto',
            $priceText
        );
    }

    protected function buildOpenAiPrompt(string $query, array $products): string
    {
        $catalogSummary = collect($products)
            ->map(function ($product) {
                $price = collect($product['variants'] ?? [])->min('price');
                $priceText = is_numeric($price) ? 'S/ ' . number_format((float) $price, 2) : 'precio no disponible';

                return sprintf(
                    '- %s (%s) %s, %s. %s',
                    $product['name'] ?? 'Producto',
                    $product['brand'] ?? 'Sin marca',
                    $priceText,
                    $product['subcategory'] ?? 'Sin subcategoria',
                    $product['match_reason'] ?? ''
                );
            })
            ->implode("\n");

        return "Consulta del cliente: {$query}\n" .
            "Productos recomendados desde base de datos:\n{$catalogSummary}\n" .
            'Responde con recomendacion breve, clara y orientada a compra, mencionando por que conviene la opcion principal. Usa unicamente productos del listado.';
    }

    /**
     * Sincroniza el catálogo completo con la IA.
     *
     * @return array Resultado de la sincronización
     */
    public function syncCatalog(): array
    {
        if (!$this->aiApiUrl) {
            throw new Exception('IA_API_URL no está configurado para sincronizar catálogo externo.');
        }

        try {
            // 1. Obtener todos los productos
            $products = $this->repository->getAllForAI();

            // 2. Transformar a formato AI
            $productsData = ProductIaResource::collection($products)->resolve();

            Log::info('Sincronizando catálogo con IA', [
                'total_products' => count($productsData)
            ]);

            // 3. Enviar a Python
            $response = Http::timeout(240) // 2 minutos para catálogos grandes
                ->post("{$this->aiApiUrl}/sync-catalog", [
                    'products' => $productsData
                ]);

            if ($response->failed()) {
                throw new Exception('Error al sincronizar catálogo: ' . $response->body());
            }

            $result = $response->json();

            Log::info('Catálogo sincronizado exitosamente', $result);

            return $result;
        } catch (Exception $e) {
            Log::error('Catalog Sync Error', [
                'message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Sincroniza un producto específico con la IA.
     *
     * Útil cuando se crea/edita un solo producto.
     */
    public function syncProduct(int $productId): void
    {
        try {
            // En lugar de sincronizar solo 1, re-sincronizamos todo
            // Es más simple y asegura consistencia
            $this->syncCatalog();
        } catch (Exception $e) {
            Log::error('Product Sync Error', [
                'product_id' => $productId,
                'message' => $e->getMessage()
            ]);

            // No lanzamos excepción para no bloquear la creación/edición del producto
            // Solo logueamos el error
        }
    }
}
