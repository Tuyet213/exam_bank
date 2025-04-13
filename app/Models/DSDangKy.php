<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class DSDangKy extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_bo_mon',
        'hoc_ki',
        'thoi_gian',
        'able'
    ];


    public function namHoc()
    {
        $thoiGian = Carbon::parse($this->thoi_gian);
        $nam = $thoiGian->year;
        
        if ($this->hoc_ki == '1') {
            return $nam . '-' . ($nam + 1);
        } elseif ($this->hoc_ki == '2' || $this->hoc_ki == 'Hè') {
            return ($nam - 1) . '-' . $nam;
        }
        
        return $nam ; 
    }

    public function boMon()
    {
        return $this->belongsTo(BoMon::class, 'id_bo_mon');
    }

    public function ctDSDangKies()
    {
        return $this->hasMany(CTDSDangKy::class, 'id_ds_dang_ky');
    }
}
