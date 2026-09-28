<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $status,
        public ?string $reason = null
    ) {
    }

    public function envelope(): Envelope
    {
        $statusLabel = match ($this->status) {
            'placed', 'pending' => 'New Order Placed',
            'accepted' => 'Order Accepted',
            'ready_for_pickup' => 'Ready for Pickup',
            'completed' => 'Order Completed',
            'declined' => 'Order Declined',
            'cancelled' => 'Order Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };

        $stallName = $this->order->farmer?->stall_name ?? 'Farmer Stall';
        $orderNum = $this->order->order_number;

        return new Envelope(
            subject: "[MarketLink Admin] Order #{$orderNum} Status: {$statusLabel} ({$stallName})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.order_status_update',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
