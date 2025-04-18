<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BienBanHop extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'cap',
        'dia_diem',
        'noi_dung',
        'thoi_gian',
        'id_ct_ds_dang_ky',
        'id_ds_dang_ky',
        'able'
    ];

    public function ctDSDangKy()
    {
        return $this->belongsTo(CTDSDangKy::class, 'id_ct_ds_dang_ky');
    }

    public function dsDangKy()
    {
        return $this->belongsTo(DSDangKy::class, 'id_ds_dang_ky');
    }

    public function dsHop()
    {
        return $this->hasMany(DSHop::class, 'id_bien_ban_hop');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bienBanHop) {
            // Kiểm tra xem có ít nhất một trong hai trường được điền
            if (is_null($bienBanHop->id_ct_ds_dang_ky) && is_null($bienBanHop->id_ds_dang_ky)) {
                throw new \Exception('Phải có ít nhất một trong hai trường id_ct_ds_dang_ky hoặc id_ds_dang_ky');
            }

            // Kiểm tra không được điền cả hai trường
            if (!is_null($bienBanHop->id_ct_ds_dang_ky) && !is_null($bienBanHop->id_ds_dang_ky)) {
                throw new \Exception('Không thể điền cả hai trường id_ct_ds_dang_ky và id_ds_dang_ky');
            }
        });
    }
}
