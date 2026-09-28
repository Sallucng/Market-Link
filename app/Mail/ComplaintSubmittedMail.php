<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Complaint $complaint)
    {
    }

    public function envelope(): Envelope
    {
        $stall = $this->complaint->farmer?->stall_name ?? 'Farmer';
        return new Envelope(
            subject: "MarketLink Alert: New Customer Complaint regarding {$stall}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.complaints.submitted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
