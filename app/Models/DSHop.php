<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DSHop extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_bien_ban_hop',
        'id_nhiem_vu',
        'id_vien_chuc',
        'so_gio',
        'able'
    ];


    public function bienBanHop()
    {
        return $this->belongsTo(BienBanHop::class, 'id_bien_ban_hop');
    }

    public function nhiemVu()
    {
        return $this->belongsTo(NhiemVu::class, 'id_nhiem_vu');
    }

    public function vienChuc()
    {
        return $this->belongsTo(User::class, 'id_vien_chuc');
    }

    

    public function dsHop()
    {
        return $this->hasMany(DSHop::class, 'id_ds_hop');
    }

}
