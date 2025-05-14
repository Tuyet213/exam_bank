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
    public $attachments;

    public function __construct($title, $content, $attachments = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->attachments = $attachments;
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
        $attachmentArray = [];
        if (is_array($this->attachments)) {
            foreach ($this->attachments as $filePath) {
                $attachmentArray[] = \Illuminate\Mail\Mailables\Attachment::fromPath($filePath);
            }
        } elseif ($this->attachments) {
            $attachmentArray[] = \Illuminate\Mail\Mailables\Attachment::fromPath($this->attachments);
        }
        return $attachmentArray;
    }
}
