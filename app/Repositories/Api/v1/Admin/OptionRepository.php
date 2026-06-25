<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\OptionInterface;
use App\Models\Option;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OptionRepository extends BaseRepository implements OptionInterface
{
    public function __construct(Option $model)
    {
        parent::__construct($model);
    }

    public function getById(int $id): Model
    {
        return $this->model::with('optionValues')->findOrFail($id);
    }

    public function create(array $data): Model
    {
        DB::beginTransaction();
        try{
            $option = Option::create([
                'name' => $data['name'],
                'type' => $data['type'],
            ]);

            foreach ($data['option_values'] as $optionValue) {
                $option->optionValues()->create([
                    'value' => $optionValue['value'],
                    'description' => $optionValue['description']
                ]);
            }

            DB::commit();

            return $option->refresh()->load('optionValues');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update(array $optionData, array $optionValuesData, int $id): Model
    {
        DB::beginTransaction();

        try {
            $option = $this->getById($id);

            // 1️⃣ Actualizar los datos principales de la opción
            if (!empty($optionData)) {
                $option->update($optionData);
            }

            // 2️⃣ Manejo de OptionValues (HasMany)
            if (array_key_exists('option_values', $optionValuesData)) {

                $incoming = collect($optionValuesData['option_values']);

                // IDs actuales en la BD
                $currentIds = $option->optionValues()->pluck('id')->toArray();

                // IDs enviados desde el frontend
                $incomingIds = $incoming->pluck('id')->filter()->toArray();

                // 2.1️⃣   Eliminar valores que el usuario quitó en el frontend
                $toDelete = array_diff($currentIds, $incomingIds);

                if (!empty($toDelete)) {
                    $option->optionValues()->whereIn('id', $toDelete)->delete();
                }

                // 2.2️⃣   Crear nuevos o actualizar existentes
                foreach ($incoming as $item) {

                    $option->optionValues()->updateOrCreate(
                        [
                            'id' => $item['id'] ?? null,  // null => crear nuevo
                        ],
                        [
                            'value'       => $item['value'],
                            'description' => $item['description'] ?? null,
                        ]
                    );
                }
            }

            DB::commit();

            return $option->refresh()->load('optionValues');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getOptionValues(int $id): Collection
    {
        $model = $this->getById($id);
        return $model->optionValues()->get(['id', 'description']);
    }
}
