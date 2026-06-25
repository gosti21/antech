<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Exports\LowStockExport;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\BranchVariant;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Reporte de Stock Bajo
     */
    public function lowStock(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:pdf,excel',
        ]);

        // Obtener variantes con stock <= stock_min en sucursal ID 1
        $lowStockItems = BranchVariant::where('branch_id', 1)
            ->whereColumn('stock', '<=', 'stock_min')
            ->with(['variant.product.brand', 'variant.optionProductValues.optionValue.option'])
            ->get()
            ->map(function ($item) {
                return [
                    'sku' => $item->variant->sku,
                    'product_name' => $item->variant->getFullNameAttribute(),
                    'current_stock' => $item->stock,
                    'min_stock' => $item->stock_min,
                    'selling_price' => $item->variant->selling_price,
                    'brand' => $item->variant->product->brand->name ?? 'Sin marca',
                ];
            });

        if ($request->format === 'pdf') {
            return $this->generateLowStockPdf($lowStockItems);
        }

        return $this->generateLowStockExcel($lowStockItems);
    }

    /**
     * Reporte de Ventas
     */
    public function sales(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:pdf,excel',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        // Obtener órdenes pagadas en el rango de fechas
        $orders = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ])
            ->with(['customer', 'orderDetails', 'paymentMethods'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $orders->sum('total');

        $salesData = $orders->map(function ($order) {
            return [
                'order_number' => $order->order_number,
                'date' => $order->created_at->format('d/m/Y H:i'),
                'customer' => $order->customer
                    ? ($order->customer->name && $order->customer->last_name
                        ? $order->customer->name . ' ' . $order->customer->last_name
                        : $order->customer->business_name)
                    : null,
                'payment_methods' => $order->paymentMethods->pluck('name')->implode(', '),
                'subtotal' => $order->subtotal,
                'igv' => $order->igv,
                'total' => $order->total,
                'status' => $order->status,
            ];
        });

        if ($request->format === 'pdf') {
            return $this->generateSalesPdf($salesData, $totalSales, $request->date_from, $request->date_to);
        }

        return $this->generateSalesExcel($salesData, $totalSales, $request->date_from, $request->date_to);
    }

    // ==================== MÉTODOS PRIVADOS PDF ====================

    private function generateLowStockPdf($items): JsonResponse
    {
        $pdf = Pdf::loadView('Reports.low-stock-pdf', [
            'items' => $items,
            'date' => now()->format('d/m/Y'),
            'hasItems' => $items->isNotEmpty()
        ]);

        $filename = 'reporte-stock-bajo-' . now()->format('Y-m-d') . '.pdf';
        $base64 = base64_encode($pdf->output());

        return response()->json([
            'file' => 'data:application/pdf;base64,' . $base64,
            'filename' => $filename
        ]);
    }

    private function generateSalesPdf($sales, $totalSales, $dateFrom, $dateTo): JsonResponse
    {
        $pdf = Pdf::loadView('Reports.sales-pdf', [
            'sales' => $sales,
            'totalSales' => $totalSales,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedDate' => now()->format('d/m/Y H:i')
        ]);

        $filename = 'reporte-ventas-' . $dateFrom . '-al-' . $dateTo . '.pdf';
        $base64 = base64_encode($pdf->output());

        return response()->json([
            'file' => 'data:application/pdf;base64,' . $base64,
            'filename' => $filename
        ]);
    }

    // ==================== MÉTODOS PRIVADOS EXCEL ====================

    private function generateLowStockExcel($items): JsonResponse
    {
        $filename = 'reporte-stock-bajo-' . now()->format('Y-m-d') . '.xlsx';

        $excel = Excel::raw(new LowStockExport($items), \Maatwebsite\Excel\Excel::XLSX);
        $base64 = base64_encode($excel);

        return response()->json([
            'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,' . $base64,
            'filename' => $filename
        ]);
    }

    private function generateSalesExcel($sales, $totalSales, $dateFrom, $dateTo): JsonResponse
    {
        $filename = 'reporte-ventas-' . $dateFrom . '-al-' . $dateTo . '.xlsx';

        $excel = Excel::raw(new SalesExport($sales, $totalSales, $dateFrom, $dateTo), \Maatwebsite\Excel\Excel::XLSX);
        $base64 = base64_encode($excel);

        return response()->json([
            'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,' . $base64,
            'filename' => $filename
        ]);
    }
}
