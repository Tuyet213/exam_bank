<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\BienBanHop;
use App\Models\User;
use App\Models\BoMon;

class NotifyCompletedBienSoan extends Mailable
{
    use Queueable, SerializesModels;

    public $bien_ban;
    public $nguoi_gui;
    public $bo_mon;

    /**
     * Create a new message instance.
     */
    public function __construct(BienBanHop $bien_ban, User $nguoi_gui, BoMon $bo_mon)
    {
        $this->bien_ban = $bien_ban;
        $this->nguoi_gui = $nguoi_gui;
        $this->bo_mon = $bo_mon;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông Báo: Hoàn thành biên soạn học phần ' . $this->bien_ban->ctDSDangKy->hocPhan->ten,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notify-completed-bien-soan',
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