<?php

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vouchers/{order}/download', function (Order $order) {
    $voucher = $order->voucher;

    abort_unless($voucher?->path, 404, 'Comprobante no disponible');

    $voucherUrl = (string) $voucher->path;
    $appUrl = rtrim((string) config('app.url'), '/');

    if (str_starts_with($voucherUrl, $appUrl . $appUrl)) {
        $voucherUrl = substr($voucherUrl, strlen($appUrl));
    }

    $parsedPath = parse_url($voucherUrl, PHP_URL_PATH) ?: '';
    $fileName = sprintf('%s-%s.pdf', strtolower((string) $voucher->type), $order->order_number);

    if (str_starts_with($voucherUrl, $appUrl . '/storage/')) {
        $relativePath = ltrim(str_replace('/storage/', '', $parsedPath), '/');
        abort_unless(Storage::disk('public')->exists($relativePath), 404, 'Archivo no encontrado');

        return Storage::disk('public')->download($relativePath, $fileName);
    }

    $response = Http::timeout(60)->get($voucherUrl);
    abort_unless($response->successful(), 404, 'No se pudo descargar el comprobante');

    return response($response->body(), 200, [
        'Content-Type' => $response->header('Content-Type', 'application/pdf'),
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ]);
})->name('vouchers.download');
