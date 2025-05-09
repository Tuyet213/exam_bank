<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ChuanDauRa extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten',
        'noi_dung',
        'able',
        'id_hoc_phan'
        // 'id_chuong'     
    ];

    

    // public function chuong()
    // {
    //     return $this->belongsTo(Chuong::class, 'id_chuong');
    // }
    public function hocPhan()
    {
        return $this->belongsTo(HocPhan::class, 'id_hoc_phan');
    }

    public function maTrans()
    {
        return $this->hasMany(MaTran::class, 'id_chuan_dau_ra');
    }

    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'id_chuan_dau_ra');
    }

    public function chuongChuanDauRa()
    {
        return $this->hasMany(ChuongChuanDauRa::class, 'id_chuong_chuan_dau_ra');
    }
}
