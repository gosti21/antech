<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\OrderInterface;
use App\Exceptions\Api\v1\NotFoundException;
use App\Models\Address;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderService
{
    public function __construct(
        protected OrderInterface $repository
    ) {}

    public function getAll(int $pagination = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($pagination);
    }

    public function getPdf(int $id)
    {
        try {
            $order = $this->repository->getById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        }

        $order->load(['branchVariants','voucher']);

        $addressId = $order->checkout_snapshot['address_id'];
        $address = Address::withTrashed()->findOrFail($addressId);;

        // Generar PDF
        $pdf = Pdf::loadView('Admin.pdfs.order-packing', [
            'order' => $order,
            'address' => $address
        ]);

        // Configuración del PDF
        $pdf->setPaper('a4', 'portrait');

        // Retornar para descarga
        return $pdf->download("orden-{$order->order_number}.pdf");

        // O si quieres mostrarlo en el navegador:
        /* return $pdf->stream("orden-{$order->order_number}.pdf"); */
    }

    public function update(array $data, $id)
    {
        try {
            return $this->repository->update($data, $id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }
}
