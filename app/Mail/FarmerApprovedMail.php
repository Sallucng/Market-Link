<?php

namespace App\Mail;

use App\Models\Farmer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FarmerApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Farmer $farmer)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎉 Congratulations! Your Farmer Stall '{$this->farmer->stall_name}' Has Been Approved on MarketLink",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.farmers.approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
