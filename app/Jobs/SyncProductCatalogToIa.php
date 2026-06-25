<?php

namespace App\Jobs;

use App\Services\Api\v1\Ia\RecommendationIAService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductCatalogToIa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos del job
     */
    public $tries = 3;

    /**
     * Timeout en segundos
     */
    public $backoff = [180, 260, 360];

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('ia');
    }

    /**
     * Execute the job.
     */
    public function handle(RecommendationIAService $service): void
    {
        Log::info('Iniciando sincronización de catálogo con IA (Job)');

        try {
            $service->syncCatalog();

            Log::info('Sincronización completada exitosamente (Job)');
        } catch (\Exception $e) {
            Log::error('Error en sincronización de catálogo (Job)', [
                'error' => $e->getMessage()
            ]);

            // Re-lanzar excepción para que el job se marque como fallido
            throw $e;
        }
    }
}
