<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Variant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Obtener estadísticas generales del dashboard
     */
    public function getStats(): JsonResponse
    {
        // Total de ventas (solo órdenes pagadas)
        $totalSales = Order::where('payment_status', 'paid')->sum('total');

        // Total de órdenes (solo pagadas)
        $totalOrders = Order::where('payment_status', 'paid')->count();

        // Total de variantes activas
        $totalVariants = Variant::where('status', true)->count();

        // Total de clientes
        $totalCustomers = Customer::count();

        return response()->json([
            'total_sales' => round($totalSales, 2),
            'total_orders' => $totalOrders,
            'total_products' => $totalVariants, // Cambiamos a variantes
            'total_customers' => $totalCustomers,
        ]);
    }

    /**
     * Obtener datos para gráfico de ventas mensuales (últimos 6 meses)
     */
    public function getSalesChart(): JsonResponse
    {
        $salesData = Order::where('payment_status', 'paid')
    ->select(
        DB::raw("DATE_FORMAT(created_at, '%b') as month"),
        DB::raw("DATE_FORMAT(created_at, '%Y-%m-01') as month_date"),
        DB::raw("SUM(total) as total")
    )
    ->where('created_at', '>=', now()->subMonths(6))
    ->groupBy(
        DB::raw("DATE_FORMAT(created_at, '%Y-%m-01')"),
        DB::raw("DATE_FORMAT(created_at, '%b')")
    )
    ->orderBy(DB::raw("month_date"))
    ->get();

        // Generar los últimos 6 meses
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'month' => $date->format('M'),
                'total' => 0
            ]);
        }

        // Merge con los datos reales
        $labels = $months->pluck('month')->toArray();
        $data = $months->map(function ($month) use ($salesData) {
            $found = $salesData->firstWhere('month', $month['month']);
            return $found ? round($found->total, 2) : 0;
        })->toArray();

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    /**
     * Obtener las 5 variantes más vendidas
     */
    public function getTopVariants(): JsonResponse
    {
        $topVariants = DB::table('order_detail')
            ->join('branch_variant', 'order_detail.branch_variant_id', '=', 'branch_variant.id')
            ->join('variants', 'branch_variant.variant_id', '=', 'variants.id')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->join('orders', 'order_detail.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'order_detail.product_name as name',
                DB::raw('SUM(order_detail.quantity) as sales')
            )
            ->groupBy('order_detail.product_name')
            ->orderByDesc('sales')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'sales' => (int) $item->sales,
                ];
            });

        return response()->json($topVariants);
    }

    /**
     * Obtener las 5 categorías más demandadas
     */
    public function getTopCategories(): JsonResponse
    {
        $topCategories = DB::table('order_detail')
            ->join('branch_variant', 'order_detail.branch_variant_id', '=', 'branch_variant.id')
            ->join('variants', 'branch_variant.variant_id', '=', 'variants.id')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->join('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->join('orders', 'order_detail.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'categories.name',
                DB::raw('SUM(order_detail.quantity) as sales')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('sales')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'sales' => (int) $item->sales,
                ];
            });

        return response()->json($topCategories);
    }

    /**
     * Obtener las 5 marcas más vendidas
     */
    public function getTopBrands(): JsonResponse
    {
        $topBrands = DB::table('order_detail')
            ->join('branch_variant', 'order_detail.branch_variant_id', '=', 'branch_variant.id')
            ->join('variants', 'branch_variant.variant_id', '=', 'variants.id')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->join('orders', 'order_detail.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'brands.name',
                DB::raw('SUM(order_detail.quantity) as sales')
            )
            ->groupBy('brands.id', 'brands.name')
            ->orderByDesc('sales')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'sales' => (int) $item->sales,
                ];
            });

        return response()->json($topBrands);
    }
}
