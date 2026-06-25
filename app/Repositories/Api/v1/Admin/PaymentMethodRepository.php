<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\PaymentMethodInterface;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentMethodRepository implements PaymentMethodInterface
{
    public function getAllList(): Collection
    {
        return PaymentMethod::where('required_qr', true)->get();
    }

    public function getById(int $id): Model
    {
        return PaymentMethod::where('required_qr', true)->findOrFail($id);
    }

    public function update(?string $imagePath, int $id): Model
    {
        DB::beginTransaction();
        try {
            $paymentMethod = $this->getById($id);

            if ($imagePath) {
                if ($paymentMethod->image) {
                    $paymentMethod->image->update([
                        'path' => $imagePath
                    ]);
                } else {
                    $paymentMethod->image()->create([
                        'path' => $imagePath
                    ]);
                }
            }

            DB::commit();
            return $paymentMethod->refresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
