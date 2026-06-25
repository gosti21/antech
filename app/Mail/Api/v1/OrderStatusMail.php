<?php

namespace App\Mail\Api\v1;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $status;
    public string $type;
    public ?array $additionalData;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $status, string $type = 'order', ?array $additionalData = null)
    {
        $this->order = $order;
        $this->status = $status;
        $this->type = $type;
        $this->additionalData = $additionalData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getSubject(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->getViewName(),
            with: [
                'order' => $this->order,
                'status' => $this->status,
                'additionalData' => $this->additionalData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    private function getSubject(): string
    {
        $subjects = [
            // Order statuses
            'confirmed' => '✅ Orden Confirmada #' . $this->order->order_number,
            'processing' => '⚙️ Tu Orden está en Proceso #' . $this->order->order_number,
            'completed' => '🎉 Orden Completada #' . $this->order->order_number,
            'cancelled' => '❌ Orden Cancelada #' . $this->order->order_number,
            'refunded' => '💰 Reembolso Procesado #' . $this->order->order_number,

            // Shipment statuses
            'preparing' => '📦 Preparando tu Envío #' . $this->order->order_number,
            'ready_for_pickup' => '✅ Tu Orden está Lista para Recoger #' . $this->order->order_number,
            'picked_up' => '✅ Orden Recogida #' . $this->order->order_number,
            'dispatched' => '🚚 Envío Despachado #' . $this->order->order_number,
            'in_transit' => '📍 Tu Pedido está en Camino #' . $this->order->order_number,
            'delivered' => '✅ Pedido Entregado #' . $this->order->order_number,
            'failed' => '⚠️ Problema con tu Envío #' . $this->order->order_number,
            'returned' => '↩️ Envío Retornado #' . $this->order->order_number,
        ];

        return $subjects[$this->status] ?? 'Actualización de tu Orden #' . $this->order->order_number;
    }

    /**
     * Get view name based on status and type
     */
    private function getViewName(): string
    {
        if ($this->type === 'shipment') {
            return 'Emails.shipment.' . $this->status;
        }

        return 'Emails.order.' . $this->status;
    }
}
