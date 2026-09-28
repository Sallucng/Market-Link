<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $status)
    {
    }

    public function envelope(): Envelope
    {
        $stallName = $this->order->farmer?->stall_name ?? 'Farmer Stall';
        
        $subject = match ($this->status) {
            'ready_for_pickup' => "🧺 Great News! Pre-Order #{$this->order->order_number} is Packed & Ready for Pickup at {$stallName}",
            'accepted' => "Pre-Order #{$this->order->order_number} Confirmed by {$stallName}",
            'completed' => "Thank You! Pre-Order #{$this->order->order_number} Completed at {$stallName}",
            'declined' => "Pre-Order #{$this->order->order_number} Update from {$stallName}",
            default => "Update on Your Pre-Order #{$this->order->order_number} from {$stallName}",
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer_status_update',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
