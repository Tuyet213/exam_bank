<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Chuong extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ten',
        'id_hoc_phan',
        'able'
    ];

    
    public function hocPhan()
    {
        return $this->belongsTo(HocPhan::class, 'id_hoc_phan');
    }

    public function maTrans()
    {
        return $this->hasMany(MaTran::class, 'id_chuong');
    }

    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'id_chuong');
    }

    public function chuanDauRas()
    {
        return $this->belongsToMany(
            ChuanDauRa::class,
            'chuong_chuan_dau_ras', // tên bảng pivot
            'id_chuong',
            'id_chuan_dau_ra'
        );
    }

    public function chuongChuanDauRa()
    {
        return $this->hasMany(ChuongChuanDauRa::class, 'id_chuong');
    }
    
}
