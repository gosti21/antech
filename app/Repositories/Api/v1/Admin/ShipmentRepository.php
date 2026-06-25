<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\ShipmentInterface;
use App\Jobs\FollowUpShipmentEmail;
use App\Models\BranchVariant;
use App\Models\Movement;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShipmentRepository implements ShipmentInterface
{
    public function getAll(int $pagination): LengthAwarePaginator
    {
        return Shipment::latest() // Ordenar por más recientes primero
            ->paginate($pagination);
    }

    public function getById(int $id): Model
    {
        return Shipment::findOrFail($id);
    }

    public function update(array $data, int $id): Model
    {
        $shipment = $this->getById($id);

        if('store_pickup' == $shipment->delivery_type){
            switch ($data['status']) {
                case 'ready_for_pickup':
                    $shipment->update([
                        'status' => $data['status'],
                    ]);
                    dispatch(new FollowUpShipmentEmail(
                        $shipment->order,
                        'ready_for_pickup',
                        'shipment'
                    ));
                    break;
                case 'picked_up':
                    $shipment->update([
                        'status' => $data['status'],
                    ]);
                    $shipment->order()->update([
                        'status' => 'completed',
                    ]);
                    dispatch(new FollowUpShipmentEmail(
                        $shipment->order,
                        'picked_up',
                        'shipment'
                    ));
                    dispatch(new FollowUpShipmentEmail(
                        $shipment->order,
                        'completed',
                        'order'
                    ));
                    break;
                default:
                    $shipment->update([
                        'status' => 'preparing',
                    ]);
            }
        }else {
            switch ($data['status']) {
                case 'returned':
                    DB::transaction(
                        function () use ($data, $shipment) {
                            if ($shipment->status === 'returned') {
                                throw new \DomainException('El envío ya fue retornado');
                            }
                            $shipment->update([
                                'status' => $data['status'],
                            ]);
                            $shipment->order()->update([
                                'status' => 'cancelled',
                            ]);
                            $order = Order::findOrFail($shipment->order->id);
                            $movement = Movement::create([
                                'movement_number' => 'Ref-' . $order->order_number,
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

                            dispatch(new FollowUpShipmentEmail(
                                $order,
                                'returned',
                                'shipment'
                            ));

                            // También enviar email de orden cancelada
                            dispatch(new FollowUpShipmentEmail(
                                $order,
                                'cancelled',
                                'order'
                            ));
                        }
                    );
                    break;
                case 'dispatched':
                    $shipment->update([
                        'status' => $data['status'],
                        'tracking_number' => $data['tracking_number'],
                        'shipping_company_id' => $data['shipping_company_id'],
                    ]);

                    dispatch(new FollowUpShipmentEmail(
                        $shipment->order,
                        'dispatched',
                        'shipment',
                        [
                            'tracking_number' => $data['tracking_number'] ?? null,
                            'shipping_company_id' => $data['shipping_company_id'] ?? null
                        ]
                    ));
                    break;
                case 'in_transit':
                    $shipment->update([
                        'status' => $data['status'],
                    ]);
                    dispatch(new FollowUpShipmentEmail(
                        $shipment->order,
                        'in_transit',
                        'shipment'
                    ));
                    break;
                case 'delivered':
                    $shipment->update([
                        'status' => $data['status'],
                    ]);
                    $shipment->order()->update([
                        'status' => 'completed',
                    ]);
                    dispatch(new FollowUpShipmentEmail(
                        $shipment->order,
                        'delivered',
                        'shipment'
                    ));

                    // También enviar email de orden completada
                    dispatch(new FollowUpShipmentEmail(
                        $shipment->order,
                        'completed',
                        'order'
                    ));
                    break;
                case 'failed':
                    $shipment->update([
                        'status' => $data['status'],
                    ]);
                    dispatch(new FollowUpShipmentEmail(
                        $shipment->order,
                        'failed',
                        'shipment'
                    ));
                    break;
                default:
                    $shipment->update([
                        'status' => 'preparing',
                    ]);
            }
        }

        return $shipment;
    }
}
