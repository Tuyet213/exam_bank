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

class NotifyRejectedBienBan extends Mailable
{
    use Queueable, SerializesModels;

    public $bienBan;
    public $nguoiNhan;
    public $lyDo;

    /**
     * Create a new message instance.
     */
    public function __construct(BienBanHop $bienBan, User $nguoiNhan, string $lyDo)
    {
        $this->bienBan = $bienBan;
        $this->nguoiNhan = $nguoiNhan;
        $this->lyDo = $lyDo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $hocPhanTen = $this->bienBan->ctDSDangKy->hocPhan->ten ?? 'không xác định';
        
        return new Envelope(
            subject: 'Thông Báo: Biên bản họp biên soạn học phần ' . $hocPhanTen . ' cần chỉnh sửa',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notify-rejected-bien-ban',
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