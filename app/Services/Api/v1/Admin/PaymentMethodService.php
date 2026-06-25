<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\PaymentMethodInterface;
use App\Exceptions\Api\v1\InternalServerErrorException;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class PaymentMethodService
{
    public function __construct(protected PaymentMethodInterface $repository) {}

    public function getAllList(): Collection
    {
        return $this->repository->getAllList();
    }

    public function getById(int $id): Model
    {
        try {
            return $this->repository->getById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }
    }

    public function update(array $data, int $id): Model
    {
        try {
            $paymentMethod = $this->repository->getById($id);

            $imagePath = null;

            if (isset($data['image'])) {
                if ($paymentMethod->image && Storage::exists($paymentMethod->image->path)) {
                    Storage::delete($paymentMethod->image->path);
                }

                $imagePath = Storage::putFile('paymentMethods', $data['image']);
            }

            return $this->repository->update($imagePath, $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        } catch (\Exception $e) {
            if (isset($imagePath)) {
                Storage::delete($imagePath);
            }

            throw new InternalServerErrorException(
                'No se pudo actualizar la foto del metodo de pago',
                $e->getMessage()
            );
        }
    }
}
