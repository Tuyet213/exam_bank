<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class LopHocPhan extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten',
        'ky_hoc',
        'nam_hoc',
        'id_khoa',
        'id_vien_chuc',
        'id_hoc_phan',
        'able'
    ];

    public function deThis()
    {
        return $this->hasMany(DeThi::class, 'id_lop');
    }

    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'id_khoa');
    }

    public function vienChuc()
    {
        return $this->belongsTo(User::class, 'id_vien_chuc');
    }

    public function hocPhan()
    {
        return $this->belongsTo(HocPhan::class, 'id_hoc_phan');
    }

}
