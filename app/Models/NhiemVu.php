<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class NhiemVu extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten',
        'able'
    ];

    public function dsHops()
    {
        return $this->hasMany(DSHop::class, 'id_nhiem_vu');
    }
}
