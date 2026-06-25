<?php

namespace App\Jobs;

use App\Contracts\Api\v1\Shop\ShipmentSInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateShipment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos del job
     */
    public $tries = 5;

    /**
     * Tiempo de espera antes de reintentar (en segundos)
     */
    public $backoff = [60, 120, 300, 450, 600];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $dataShipment,
    ) {
        $this->onQueue('general');
    }

    /**
     * Execute the job.
     */
    public function handle(ShipmentSInterface $repository): void
    {
        try {
            $shipment = $repository->create($this->dataShipment);
            Log::info('Shipment creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error creando shipment', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Manejar el fallo del job
     */
    public function failed(\Throwable $exception): void
    {
        // Aquí puedes registrar el error o notificar a los administradores
        Log::error('Error al crear el shipment', [
            'error' => $exception->getMessage()
        ]);
    }
}
