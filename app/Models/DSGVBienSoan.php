<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DSGVBienSoan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'id_ct_ds_dang_ky',
        'id_vien_chuc'
    ];
    
    public function ctDSDangKy()
    {
        return $this->belongsTo(CTDSDangKy::class, 'id_ct_ds_dang_ky');
    }
    
    public function vienChuc()
    {
        return $this->belongsTo(User::class, 'id_vien_chuc');
    }
}
