<?php

namespace App\Jobs;

use App\Mail\Api\v1\OrderStatusMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FollowUpShipmentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos del job
     */
    public $tries = 5;

    /**
     * Tiempo de espera antes de reintentar (en segundos)
     */
    public $backoff = [60, 120, 200, 300, 400];

    protected $order;
    protected $status;
    protected $type;
    protected $additionalData;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, string $status, string $type = 'order', ?array $additionalData = null)
    {
        $this->order = $order;
        $this->status = $status;
        $this->type = $type;
        $this->additionalData = $additionalData;
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->order->user || !$this->order->user->email) {
            return;
        }

        Mail::to($this->order->user->email)
            ->send(new OrderStatusMail(
                $this->order,
                $this->status,
                $this->type,
                $this->additionalData
            ));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Error al enviar email de seguimiento', [
            'order_id' => $this->order->id,
            'status' => $this->status,
            'type' => $this->type,
            'error' => $exception->getMessage()
        ]);
    }
}
