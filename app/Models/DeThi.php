<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DeThi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_hoc_phan',
        'id_lop_hoc_phan',
        'hoc_ky',
        'nam',
        'ngay_thi',
        'loai',
        'su_dung_tai_lieu',
        'able'
    ];

    public function hocPhan()
    {
        return $this->belongsTo(HocPhan::class, 'id_hoc_phan');
    }   

    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'id_lop_hoc_phan');
    }   

    

    public function ctDeThi()
    {
        return $this->hasMany(CTDeThi::class, 'id_de_thi');
    }
    
    

}
