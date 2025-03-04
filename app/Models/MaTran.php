<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class MaTran extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        //'ten',
        //'id_hoc_phan',
        'id_chuan_dau_ra',
        'id_chuong',
        'diem'
    ];


    public function chuanDauRa()
    {
        return $this->belongsTo(ChuanDauRa::class, 'id_chuan_dau_ra');
    }

    public function chuong()
    {
        return $this->belongsTo(Chuong::class, 'id_chuong');
    }
    
}
