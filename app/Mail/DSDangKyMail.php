<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DSDangKyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tieuDe;
    public $tenBoMon;
    public $tenNguoiGui;
    public $ctdsdangky;
    public $dsdangky;

    public function __construct($tieuDe, $tenBoMon, $tenNguoiGui, $ctdsdangky, $dsdangky    )
    {
        $this->tieuDe = $tieuDe;
        $this->tenBoMon = $tenBoMon;
        $this->tenNguoiGui = $tenNguoiGui;
        $this->ctdsdangky = $ctdsdangky;
        $this->dsdangky = $dsdangky;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->tieuDe,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.DSDangKyMail',
            with: [
                'tieuDe' => $this->tieuDe,
                'tenBoMon' => $this->tenBoMon,
                'tenNguoiGui' => $this->tenNguoiGui,
                'ctdsdangky' => $this->ctdsdangky,
                'dsdangky' => $this->dsdangky
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
        return [];
    }
}