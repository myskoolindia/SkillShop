<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DefaultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    public $subject;
    public $messageTemplate;

    /** Optional raw PDF bytes to attach */
    private ?string $pdfBytes;
    private ?string $pdfFilename;

    /**
     * @param array       $mailData        Must contain 'subject' key
     * @param string      $messageTemplate HTML body
     * @param string|null $pdfBytes        Raw PDF binary (from Dompdf::output())
     * @param string|null $pdfFilename     Attachment filename, e.g. "Proposal.pdf"
     */
    public function __construct(
        array $mailData,
        string $messageTemplate,
        ?string $pdfBytes = null,
        ?string $pdfFilename = null
    ) {
        $this->mailData        = $mailData;
        $this->messageTemplate = $messageTemplate;
        $this->subject         = $mailData['subject'];
        $this->pdfBytes        = $pdfBytes;
        $this->pdfFilename     = $pdfFilename ?? 'Skillvation_Proposal.pdf';
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->mailData['subject']);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.default-mail-template');
    }

    public function attachments(): array
    {
        if ($this->pdfBytes === null) {
            return [];
        }

        return [
            Attachment::fromData(
                fn () => $this->pdfBytes,
                $this->pdfFilename
            )->withMime('application/pdf'),
        ];
    }
}
