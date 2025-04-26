<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\BienBanHop;

class NotifyRejectedBienBan extends Notification implements ShouldQueue
{
    use Queueable;

    protected $bienban;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(BienBanHop $bienban, string $reason)
    {
        $this->bienban = $bienban;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $hocPhan = $this->bienban->ctDSDangKy->hocPhan;
        $namHoc = $this->bienban->ctDSDangKy->dsDangKy->nam_hoc ?? 'Không xác định';
        $hocKy = $this->bienban->ctDSDangKy->dsDangKy->hoc_ki ?? 'Không xác định';
        $giangVien = $this->bienban->ctDSDangKy->vienChuc;
        $boMon = $hocPhan->boMon ?? null;
        $boMonTen = $boMon ? $boMon->ten : 'Không xác định';

        return (new MailMessage)
            ->subject('Thông báo: Biên bản họp bộ môn cần chỉnh sửa')
            ->greeting('Kính gửi Thầy/Cô ' . $notifiable->name)
            ->line('Phòng Đảm bảo chất lượng đã xem xét và có ý kiến về biên bản họp bộ môn của Thầy/Cô.')
            ->line('Thông tin chi tiết:')
            ->line('- Học phần: ' . $hocPhan->ten . ' (Mã: ' . ($hocPhan->ma_hoc_phan ?? $hocPhan->id) . ')')
            ->line('- Năm học: ' . $namHoc)
            ->line('- Học kỳ: ' . $hocKy)
            ->line('- Giảng viên: ' . $giangVien->name)
            ->line('- Bộ môn: ' . $boMonTen)
            ->line('- Thời gian biên bản: ' . $this->bienban->thoi_gian)
            ->line('Lý do cần chỉnh sửa:')
            ->line($this->reason)
            ->line('Thầy/Cô vui lòng kiểm tra và điều chỉnh biên bản theo các góp ý trên.')
            ->salutation('Trân trọng,')
            ->line('Phòng Đảm bảo chất lượng');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
} 