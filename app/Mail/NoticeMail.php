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
    public $files;
    public function __construct($title, $content, $files)
    {
        $this->title = $title;
        $this->content = $content;
        $this->files = $files;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notice',
            with: [
                'title' => $this->title,
                'content' => $this->content,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
{
    if (empty($this->files)) {
        return [];
    }

    $attachments = [];
    foreach ($this->files as $file) {
        $attachments[] = Attachment::fromPath($file->getRealPath())
            ->as($file->getClientOriginalName())
            ->withMime($file->getMimeType());
    }
    return $attachments;
}
}
