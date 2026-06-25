<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\CreateShipment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CreateShipmentListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Número de intentos del listener
     */
    public $tries = 5;

    public $queue = 'general';

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        CreateShipment::dispatch(
            $event->dataShipment
        );
    }

    public function failed(OrderCreated $event, \Throwable $exception): void
    {
        Log::error('Error en el listener de creación del envio', [
            'order_id' => $event->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}
