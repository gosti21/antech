<?php

namespace App\Services\Api\v1\integrations;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NiubizService
{
    protected string $baseUrl;
    protected string $merchantId;
    protected string $user;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('integrations.niubiz.url_api');
        $this->merchantId = config('integrations.niubiz.merchant_id');
        $this->user = config('integrations.niubiz.user');
        $this->password = config('integrations.niubiz.password');
    }


    public function generateAccessToken()
    {
        try {
            $url = "{$this->baseUrl}/api.security/v1/security";
            $auth = base64_encode("{$this->user}:{$this->password}");

            $response = Http::withHeaders([
                'Authorization' => "Basic {$auth}",
            ])->get($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => "HTTP {$response->status()}: {$response->body()}"
                ];
            }
            return $response->body();
        } catch (\Exception $e) {
            Log::error('Excepción al generar Access Token', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function generateSessionToken(float $total, User $user)
    {
        try {
            // Generar Access Token
            $tokenResult = $this->generateAccessToken();

            $url = "{$this->baseUrl}/api.ecommerce/v2/ecommerce/token/session/{$this->merchantId}";


            $response = Http::withHeaders([
                'Authorization' => $tokenResult,
                'Content-Type' => 'application/json'
            ])->post($url, [
                'channel' => 'web',
                'amount' => number_format($total, 2, '.', ''),
                'antifraud' => [
                    'merchantDefineData' => [
                        'MDD4' => $user->email,
                        'MDD32' => (string) $user->id,
                        'MDD75' => 'value75',
                        'MDD77' => (string) now()->diffInDays($user->created_at),
                    ]
                ],
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => "HTTP {$response->status()}: {$response->body()}"
                ];
            }

            $data = $response->json();

            if (!isset($data['sessionKey'])) {
                return [
                    'success' => false,
                    'error' => 'Session Token no encontrado en la respuesta'
                ];
            }

            return $data['sessionKey'];
        } catch (\Exception $e) {
            Log::error('Excepción al generar Session Token', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function paid($tokenPago, Order $order)
    {
        try {
            $forceApproved = (bool) config('integrations.niubiz.force_approved', false);
            $isLocal = app()->environment('local');

            // Permite validar el flujo de checkout en desarrollo sin depender del sandbox del adquirente.
            if ($forceApproved && $isLocal) {
                $mockTransactionId = 'MOCK-' . now()->format('YmdHis') . '-' . $order->id;

                Log::warning('Niubiz mock approval enabled', [
                    'order_id' => $order->id,
                ]);

                return [
                    'dataMap' => [
                        'ACTION_CODE' => '000',
                        'ACTION_DESCRIPTION' => 'Aprobado (modo prueba local)',
                        'TRANSACTION_ID' => $mockTransactionId,
                    ],
                    'order' => [
                        'transactionId' => $mockTransactionId,
                    ],
                ];
            }

            $accessToken = $this->generateAccessToken();
            $merchant_id = config('integrations.niubiz.merchant_id');
            $url_api = config('integrations.niubiz.url_api') . "/api.authorization/v3/authorization/ecommerce/{$merchant_id}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $accessToken,
            ])->post($url_api, [
                'channel' => 'web',
                'captureType' => 'manual',
                'countable' => 'true',
                'order' => [
                    'tokenId' => $tokenPago,
                    'purchaseNumber' => (string) $order->id,
                    'amount' => $order->total,
                    'currency' => 'PEN',
                ]
            ]);

            if (! $response->successful()) {

                // 🔹 1. Intentar JSON directo (Laravel)
                $error = $response->json();

                // 🔹 2. Fallback: intentar decodificar body manualmente
                if (! is_array($error)) {
                    $error = json_decode($response->body(), true);
                }

                // 🔹 3. Si aún no es array → error real del proveedor
                if (! is_array($error)) {
                    return [
                        'success' => false,
                        'error' => $response->body()
                    ];
                }

                return [
                    'success' => false,
                    'error' => [
                        'code' => $error['errorCode'] ?? null,
                        'message' => $error['errorMessage'] ?? null,
                        'action_description' => data_get($error, 'data.ACTION_DESCRIPTION'),
                        'raw' => $error
                    ]
                ];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Excepción al concretar el pago', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
