<?php

namespace App\Mail;

use App\Models\CmsKit\CareerCandidate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CareerApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CareerCandidate $candidate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('New career application: :job', ['job' => $this->candidate->apply_for]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.career-application-submitted',
            with: [
                'candidate' => $this->candidate,
                'attachmentUrl' => $this->candidate->attachment
                    ? url('/storage/'.ltrim((string) $this->candidate->attachment, '/'))
                    : null,
                'logoPath' => $this->logoPath(),
                'logoUrl' => $this->logoUrl(),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $path = $this->attachmentPath();

        if ($path === null) {
            return [];
        }

        return [
            Attachment::fromPath($path)
                ->as($this->attachmentName())
                ->withMime($this->attachmentMime($path)),
        ];
    }

    protected function attachmentPath(): ?string
    {
        $attachment = trim((string) $this->candidate->attachment);

        if ($attachment === '' || preg_match('#^https?://#i', $attachment)) {
            return null;
        }

        $normalized = str_replace('\\', '/', $attachment);
        $normalized = preg_replace('#^/?storage/#', '', $normalized) ?? $normalized;
        $normalized = ltrim($normalized, '/');

        try {
            $localPath = Storage::disk('public')->path($normalized);
        } catch (\Throwable) {
            return null;
        }

        return is_file($localPath) ? $localPath : null;
    }

    protected function attachmentName(): string
    {
        $extension = pathinfo((string) $this->candidate->attachment, PATHINFO_EXTENSION);
        $base = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $this->candidate->name) ?? '', '-');
        $base = $base !== '' ? $base : 'career-application';

        return $extension !== '' ? "{$base}-attachment.{$extension}" : "{$base}-attachment";
    }

    protected function attachmentMime(string $path): string
    {
        return mime_content_type($path) ?: 'application/octet-stream';
    }

    protected function logoPath(): ?string
    {
        $fallback = public_path('images/logo.png');

        return is_file($fallback) ? $fallback : null;
    }

    protected function logoUrl(): string
    {
        return asset('images/logo.png');
    }
}
