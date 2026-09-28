<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderFarmerAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        $customerName = $this->order->customer?->name ?? 'Customer';
        return new Envelope(
            subject: "New Harvest Pre-Order #{$this->order->order_number} Received from {$customerName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.farmer_alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
