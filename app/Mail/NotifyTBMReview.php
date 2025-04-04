<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\DSDangKy;

class NotifyTBMReview extends Mailable
{
    use Queueable, SerializesModels;

    public $dsDangKy;


    /**
     * Create a new message instance.
     */
    public function __construct(DSDangKy $dsDangKy)
    {
        $this->dsDangKy = $dsDangKy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông Báo Xem Xét Kết Quả Đăng Ký',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notify-tbm-review',
            with: [
                'boMon' => $this->dsDangKy->boMon,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 