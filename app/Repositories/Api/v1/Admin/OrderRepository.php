<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\OrderInterface;
use App\Jobs\FollowUpShipmentEmail;
use App\Models\BranchVariant;
use App\Models\Movement;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderInterface
{
    public function getAll(int $pagination): LengthAwarePaginator
    {
        return Order::where('type_sale', 'online')
            ->where(function ($query) {
                $query->whereIn('status', ['confirmed', 'processing', 'ready', 'completed', 'refunded'])
                    ->orWhere(function ($q) {
                        $q->where('status', 'cancelled')
                            ->where('payment_status', 'paid');
                    });
            })
            ->latest() // Ordenar por más recientes primero
            ->paginate($pagination);
    }

    public function getById(int $id): Model
    {
        return Order::where('id', $id)
            ->where('type_sale', 'online')
            ->whereIn('status', ['confirmed', 'processing', 'ready', 'completed', 'refunded'])
            ->firstOrFail();
    }

    public function update(array $data, int $id): Model
    {
        $order = $this->getById($id);

        switch($data['status']) {
            case 'refunded':
                DB::transaction(
                    function () use ($data, $order) {
                        if ($order->status === 'refunded') {
                            throw new \DomainException('La orden ya fue reembolsada');
                        }
                        $order->update([
                            'status' => $data['status'],
                            'payment_status' => 'refunded',
                            'checkout_snapshot' => null
                        ]);
                        $order->shipment()->update([
                            'status' => 'cancelled',
                        ]);
                        $movement = Movement::create([
                            'movement_number' => 'Ref-'.$order->order_number,
                            'type' => 'inflow',
                            'reason' => 'return',
                            'detail_transaction' => 'Regreso del producto',
                            'order_id' => $order->id
                        ]);
                        foreach ($order->branchVariants as $item) {
                            $movement->branchVariants()->attach($item->id, [
                                'quantity' => $item->pivot->quantity
                            ]);
                            $branchVariant = BranchVariant::findOrFail($item->id);
                            $branchVariant->increment('stock', $item->pivot->quantity);
                        }

                        dispatch(new FollowUpShipmentEmail($order, 'refunded', 'order'));
                    });
                break;
            case 'processing':
                $order->update([
                    'status' => $data['status'],
                ]);
                $order->shipment()->update([
                    'status' => 'preparing'
                ]);
                dispatch(new FollowUpShipmentEmail($order, 'processing', 'order'));
                break;
            default:
                $order->update([
                    'status' => 'confirmed',
                ]);
        }

        return $order;
    }
}
