<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\VariantInterface;
use App\Exceptions\Api\v1\BadRequestException;
use App\Exceptions\Api\v1\InternalServerErrorException;
use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Variant;
use App\traits\SkuGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * @extends BaseService<VariantInterface>
 */
class VariantService extends BaseService
{
    use SkuGenerator;

    public function __construct(VariantInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getAllShort(int $id, int $pagination = 15): LengthAwarePaginator
    {
        return $this->repository->getAllShort($pagination, $id);
    }

    public function create(array $data): Model
    {
        $sku = $this->generateSkuVariant($data['product_id']);
        $data['sku'] = $sku;

        $images = [];

        try {
            $variantData = Arr::only($data, [
                'selling_price',
                'purcharse_price',
                'product_id',
                'sku',
            ]);

            // Procesar imágenes
            foreach ($data['images'] as $image) {
                $path = Storage::putFile('variants', $image['image']);
                $images[] = $path;
            }

            // Preparar features
            $variantFeatures = collect($data['features'])
                ->pluck('option_product_value')
                ->sort()
                ->values()
                ->toArray();

            // ✅ VALIDACIÓN 1: Verificar que la variante tenga todas las opciones del producto
            $this->validateVariantHasAllProductOptions(
                $data['product_id'],
                $variantFeatures
            );

            // ✅ VALIDACIÓN 2: Verificar que la combinación sea única
            $this->validateUniqueCombination(
                $data['product_id'],
                $variantFeatures
            );

            return $this->repository->create($variantData, $images, $variantFeatures, $data['stock_min']);
        } catch (ValidationException | BadRequestException $e) {
            // Limpiar imágenes en caso de error de validación
            if (!empty($images)) {
                foreach ($images as $imagePath) {
                    Storage::delete($imagePath);
                }
            }
            throw $e;
        } catch (\Exception $e) {
            // Limpiar imágenes en caso de error general
            if (!empty($images)) {
                foreach ($images as $imagePath) {
                    Storage::delete($imagePath);
                }
            }
            throw new InternalServerErrorException(
                'No se pudo crear la variante',
                $e->getMessage()
            );
        }
    }

    public function update(array $data, int $id): Model
    {
        $variantData = Arr::only($data, [
            'selling_price',
            'purcharse_price',
            'product_id',
            'status'
        ]);

        $images = null;
        if (isset($data['image'])) {
            $images = [];
            foreach ($data['images'] as $image) {
                $path = Storage::putFile('variants', $image['image']);
                $images[] = $path;
            }
        }

        $variantFeatures = null;
        if (isset($data['features'])) {
            $variantFeatures = collect($data['features'])
                ->pluck('option_product_value')
                ->sort()
                ->values()
                ->toArray();

            // ✅ VALIDACIÓN 1: Verificar que la variante tenga todas las opciones del producto
            if (isset($variantData['product_id'])) {
                $this->validateVariantHasAllProductOptions(
                    $variantData['product_id'],
                    $variantFeatures
                );
            }

            // ✅ VALIDACIÓN 2: Verificar que la combinación sea única (excluyendo la variante actual)
            $productId = $variantData['product_id'] ?? $this->repository->getById($id)->product_id;
            $this->validateUniqueCombination(
                $productId,
                $variantFeatures,
                $id
            );
        }

        try {
            return $this->repository->update(
                $variantData,
                $data['stock_min'] ?? null,
                $images,
                $variantFeatures,
                $id
            );
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        } catch (ValidationException | BadRequestException $e) {
            // Limpiar imágenes en caso de error de validación
            if (!empty($images)) {
                foreach ($images as $imagePath) {
                    Storage::delete($imagePath);
                }
            }
            throw $e;
        } catch (\Exception $e) {
            // Limpiar imágenes en caso de error general
            if (!empty($images)) {
                foreach ($images as $imagePath) {
                    Storage::delete($imagePath);
                }
            }
            throw new InternalServerErrorException(
                'No se pudo actualizar la variante',
                $e->getMessage()
            );
        }
    }

    /**
     * ✅ VALIDACIÓN 1: Verificar que la variante tenga exactamente una feature por cada opción del producto
     *
     * Asegura que:
     * - La variante tenga todas las opciones del producto
     * - No tenga opciones de más
     * - No tenga opciones duplicadas (ej: dos colores en una variante)
     *
     * @param int $productId ID del producto
     * @param array $variantFeatures IDs de option_product_value
     * @throws BadRequestException
     */
    protected function validateVariantHasAllProductOptions(int $productId, array $variantFeatures): void
    {
        // Obtener todas las opciones configuradas en el producto
        $productOptions = DB::table('option_product')
            ->where('product_id', $productId)
            ->pluck('option_id')
            ->toArray();

        // Validar que el producto tenga opciones configuradas
        if (empty($productOptions)) {
            throw new BadRequestException(
                'El producto no tiene opciones configuradas. Configure las opciones antes de crear variantes.'
            );
        }

        // Obtener las opciones de las features enviadas
        $variantOptionsData = DB::table('option_product_value as opv')
            ->join('option_product as op', 'opv.option_product_id', '=', 'op.id')
            ->whereIn('opv.id', $variantFeatures)
            ->where('op.product_id', $productId)
            ->select('op.option_id', DB::raw('count(*) as count'))
            ->groupBy('op.option_id')
            ->get();

        $variantOptionIds = $variantOptionsData->pluck('option_id')->toArray();
        $optionCounts = $variantOptionsData->pluck('count', 'option_id')->toArray();

        // ❌ VALIDAR: Opciones faltantes
        $missingOptions = array_diff($productOptions, $variantOptionIds);
        if (!empty($missingOptions)) {
            $optionNames = DB::table('options')
                ->whereIn('id', $missingOptions)
                ->pluck('name')
                ->toArray();

            throw new BadRequestException(
                'La variante debe tener todas las opciones del producto. Faltan: ' . implode(', ', $optionNames)
            );
        }

        // ❌ VALIDAR: Opciones extras (que no pertenecen al producto)
        $extraOptions = array_diff($variantOptionIds, $productOptions);
        if (!empty($extraOptions)) {
            $optionNames = DB::table('options')
                ->whereIn('id', $extraOptions)
                ->pluck('name')
                ->toArray();

            throw new BadRequestException(
                'La variante contiene opciones que no pertenecen al producto: ' . implode(', ', $optionNames)
            );
        }

        // ❌ VALIDAR: Opciones duplicadas (ej: dos colores en una variante)
        $duplicatedOptions = array_filter($optionCounts, fn($count) => $count > 1);
        if (!empty($duplicatedOptions)) {
            $optionNames = DB::table('options')
                ->whereIn('id', array_keys($duplicatedOptions))
                ->pluck('name')
                ->toArray();

            throw new BadRequestException(
                'Una variante no puede tener múltiples valores de la misma opción. Opciones duplicadas: ' . implode(', ', $optionNames)
            );
        }
    }

    /**
     * ✅ VALIDACIÓN 2: Verificar que no exista otra variante con la misma combinación de features
     *
     * Evita duplicados como:
     * - Variante 1: Rojo + L
     * - Variante 2: Rojo + L (DUPLICADO ❌)
     *
     * @param int $productId ID del producto
     * @param array $variantFeatures IDs de option_product_value
     * @param int|null $excludeVariantId ID de la variante a excluir (para updates)
     * @throws ValidationException
     */
    private function validateUniqueCombination(int $productId, array $variantFeatures, ?int $excludeVariantId = null): void
    {
        $features = collect($variantFeatures)->sort()->values()->toArray();

        $query = Variant::where('product_id', $productId)
            ->whereHas('optionProductValues', function ($q) use ($features) {
                $q->whereIn('option_product_value_id', $features);
            }, '=', count($features))
            ->whereDoesntHave('optionProductValues', function ($q) use ($features) {
                $q->whereNotIn('option_product_value_id', $features);
            });

        // Excluir la variante actual en caso de update
        if ($excludeVariantId !== null) {
            $query->where('id', '!=', $excludeVariantId);
        }

        $exists = $query->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'features' => ['Ya existe una variante con esta combinación de características']
            ]);
        }
    }
}
