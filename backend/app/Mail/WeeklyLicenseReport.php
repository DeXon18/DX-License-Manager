<?php

namespace App\Mail;

use App\Models\LicenseArchive;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class WeeklyLicenseReport extends Mailable
{
    use Queueable, SerializesModels;

    public $archive;

    /**
     * Create a new message instance.
     */
    public function __construct(LicenseArchive $archive)
    {
        $this->archive = $archive;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Repositorio Semanal de Licencias - S{$this->archive->week_number} / {$this->archive->year}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-report',
        );
    }

    /**
     * Get the attachments for the message.
     * Adjuntamos el ZIP directamente al correo de soporte.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->archive->full_path)
                ->as($this->archive->filename)
                ->withMime('application/zip'),
        ];
    }
}
