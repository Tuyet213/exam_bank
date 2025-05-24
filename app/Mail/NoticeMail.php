<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class NoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $content;
    public $attachment;

    public function __construct($title, $content, $attachment = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->attachment = $attachment;
    }

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Thông báo: ' . $this->title,
        );
    }

    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.notice',
        );
    }

    public function attachments(): array
    {
        if ($this->attachment) {
            return [\Illuminate\Mail\Mailables\Attachment::fromPath($this->attachment)];
        }
        return [];
    }
}
