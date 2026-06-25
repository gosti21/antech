<?php

namespace App\Repositories\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\OrderMInterface;
use App\Filters\Api\v1\Mobile\Orders\OrderDateMFilter;
use App\Filters\Api\v1\Mobile\Orders\OrderOrderByMFilter;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;

class OrderMRepository implements OrderMInterface
{
    public function getAll(int $pagination): LengthAwarePaginator
    {
        $query = Order::where('type_sale', 'store');

        $filters = [
            OrderDateMFilter::class,
            OrderOrderByMFilter::class,
        ];

        $query = app(Pipeline::class)
            ->send($query)
            ->through($filters)
            ->thenReturn();

        return $query->paginate($pagination);
    }

    public function getTotals(): array
    {
        $query = Order::where('type_sale', 'store');

        $filters = [
            OrderDateMFilter::class,
        ];

        $query = app(Pipeline::class)
            ->send($query)
            ->through($filters)
            ->thenReturn();

        $totalRevenue = $query->sum('total');
        $totalItems = $query->withSum('orderDetails', 'quantity')
            ->get()
            ->sum('order_details_sum_quantity');

        return [
            'total_items' => (int) $totalItems,
            'total_sales' => (float) $totalRevenue,
        ];
    }

    public function getById(int $id): Model
    {
        return Order::where('id', $id)
            ->where('type_sale', 'store')
            ->firstOrFail();
    }

    public function create(array $orderData): Model
    {
        return Order::create($orderData);
    }

    public function createDetails(Model $order, array $items): void
    {
        foreach ($items as $item) {
            OrderDetail::create([
                'product_name' => $item['product_name'],
                'variant_sku' => $item['variant_sku'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
                'branch_variant_id' => $item['branch_variant_id'],
                'order_id' => $order->id,
            ]);
        }
    }

    public function attachPaymentMethod(Model $order, int $paymentMethodId, array $paymentData): void
    {
        $order->paymentMethods()->attach($paymentMethodId, $paymentData);
    }
}
