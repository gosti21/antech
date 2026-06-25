<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\SendVoucherEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendVoucherEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Número de intentos del listener
     */
    public $tries = 5;

    public $queue = 'emails';

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
        $user = User::find($event->userId);

        SendVoucherEmail::dispatch(
            $user,
            $event->order,
            $event->dataVoucher,
            $event->voucherType,
        );
    }

    public function failed(OrderCreated $event, \Throwable $exception): void
    {
        Log::error('Error en el listener de envio de comprobante', [
            'order_id' => $event->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}
