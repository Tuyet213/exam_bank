<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeThi extends Model
{
    use HasFactory;

    protected $table = 'de_this';

    protected $fillable = [
        'de',
        'dap_an',
        'loai',
        'id_hoc_phan',
        'id_ct_ds_dang_ky'
    ];

    // Định nghĩa các loại đề thi bằng số
    const LOAI_TRAC_NGHIEM = 0;
    const LOAI_TU_LUAN = 1;

    /**
     * Relationship với HocPhan
     */
    public function hocPhan()
    {
        return $this->belongsTo(HocPhan::class, 'id_hoc_phan');
    }   

    /**
     * Relationship với CTDSDangKy
     */
    public function ctDSDangKy()
    {
        return $this->belongsTo(CTDSDangKy::class, 'id_ct_ds_dang_ky');
    }   

    /**
     * Kiểm tra xem đề thi có phải là trắc nghiệm không
     */
    public function isTracNghiem()
    {
        return $this->loai === self::LOAI_TRAC_NGHIEM;
    }

    /**
     * Kiểm tra xem đề thi có phải là tự luận không
     */
    public function isTuLuan()
    {
        return $this->loai === self::LOAI_TU_LUAN;
    }

    /**
     * Lấy tên loại đề thi
     */
    public function getLoaiTextAttribute()
    {
        return self::getLoaiOptions()[$this->loai] ?? 'Không xác định';
    }

    /**
     * Convert từ hình thức thi (string) sang loại đề thi (number)
     */
    public static function convertHinhThucThiToLoai($hinhThucThi)
    {
        switch ($hinhThucThi) {
            case 'Trắc nghiệm':
                return self::LOAI_TRAC_NGHIEM;
            case 'Tự luận':
            case 'Tự luận/Vấn đáp':
            case 'Thực hành':
            default:
                return self::LOAI_TU_LUAN;
        }
    }

    /**
     * Lấy đường dẫn đầy đủ của file đề thi
     */
    public function getFileDeAttribute()
    {
        return $this->de ? storage_path('app/public/' . $this->de) : null;
    }

    /**
     * Lấy đường dẫn đầy đủ của file đáp án
     */
    public function getFileDapAnAttribute()
    {
        return $this->dap_an ? storage_path('app/public/' . $this->dap_an) : null;
    }

    /**
     * Lấy URL public của file đề thi
     */
    public function getDeUrlAttribute()
    {
        return $this->de ? asset('storage/' . $this->de) : null;
    }   

    /**
     * Lấy URL public của file đáp án
     */
    public function getDapAnUrlAttribute()
    {
        return $this->dap_an ? asset('storage/' . $this->dap_an) : null;
    }

    /**
     * Kiểm tra xem đề thi có file đề không
     */
    public function hasFileDE()
    {
        return !empty($this->de) && file_exists($this->file_de);
    }

    /**
     * Kiểm tra xem đề thi có file đáp án không
     */
    public function hasFileDapAn()
    {
        return !empty($this->dap_an) && file_exists($this->file_dap_an);
    }

    /**
     * Scope để lọc theo loại đề thi
     */
    public function scopeTracNghiem($query)
    {
        return $query->where('loai', self::LOAI_TRAC_NGHIEM);
    }

    public function scopeTuLuan($query)
    {
        return $query->where('loai', self::LOAI_TU_LUAN);
    }

    /**
     * Scope để lọc theo học phần
     */
    public function scopeByHocPhan($query, $hocPhanId)
    {
        return $query->where('id_hoc_phan', $hocPhanId);
    }

    /**
     * Scope để lọc theo CTDSDangKy
     */
    public function scopeByCTDSDangKy($query, $ctdsdangkyId)
    {
        return $query->where('id_ct_ds_dang_ky', $ctdsdangkyId);
    }

    /**
     * Lấy tất cả các loại đề thi có thể chọn
     */
    public static function getLoaiOptions()
    {
        return [
            self::LOAI_TRAC_NGHIEM => 'Trắc nghiệm',
            self::LOAI_TU_LUAN => 'Tự luận/Vấn đáp'
        ];
    }

    /**
     * Xóa file đề thi khi xóa record
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($deThi) {
            // Xóa file đề thi nếu tồn tại
            if ($deThi->hasFileDE()) {
                unlink($deThi->file_de);
    }
    
            // Xóa file đáp án nếu tồn tại
            if ($deThi->hasFileDapAn()) {
                unlink($deThi->file_dap_an);
            }
        });
    }
}
