<?php

namespace App\Mail;

use App\Models\CmsKit\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactEnquirySubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Enquiry $enquiry,
    ) {}

    public function envelope(): Envelope
    {
        $subject = trim((string) $this->enquiry->subject);

        return new Envelope(
            subject: $subject !== '' ? 'New contact enquiry: '.$subject : 'New contact enquiry',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-enquiry-submitted',
            with: [
                'enquiry' => $this->enquiry,
                'logoPath' => $this->logoPath(),
                'logoUrl' => asset('images/logo.png'),
            ],
        );
    }

    protected function logoPath(): ?string
    {
        $path = public_path('images/logo.png');

        return is_file($path) ? $path : null;
    }
}
