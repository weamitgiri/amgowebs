<?php

namespace App\Mail;

use App\Models\PackageInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerInquiryConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PackageInquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your inquiry — Ref '.$this->inquiry->reference,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.inquiry-customer');
    }
}
