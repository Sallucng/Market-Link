<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        $stallName = $this->order->farmer?->stall_name ?? 'Farmer Stall';
        return new Envelope(
            subject: "MarketLink Pre-Order Confirmed #{$this->order->order_number} — Pickup at {$stallName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer_confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
