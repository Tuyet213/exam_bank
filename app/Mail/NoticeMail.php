<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class NoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $content;
    public $attachments;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $content, $attachments = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->attachments = $attachments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo: ' . $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notice',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!$this->attachments) {
            return [];
        }

        $attachmentArray = [];
        
        if (is_array($this->attachments)) {
            foreach ($this->attachments as $file) {
                $attachmentArray[] = Attachment::fromPath($file->path())
                    ->as($file->getClientOriginalName())
                    ->withMime($file->getMimeType());
            }
        } else {
            $attachmentArray[] = Attachment::fromPath($this->attachments->path())
                ->as($this->attachments->getClientOriginalName())
                ->withMime($this->attachments->getMimeType());
        }

        return $attachmentArray;
    }
}
