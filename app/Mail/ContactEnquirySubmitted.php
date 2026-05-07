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
        $type = (string) data_get($this->enquiry->extra_fields, 'enquiry_type', 'form');
        $isProperty = $type === 'property';
        $isBookViewing = $type === 'book_viewing';
        $subject = trim((string) $this->enquiry->subject);
        $prefix = $isProperty ? 'New property enquiry' : ($isBookViewing ? 'New book viewing enquiry' : 'New contact enquiry');

        return new Envelope(
            subject: $subject !== '' ? $prefix.': '.$subject : $prefix,
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
