<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class BacDaoTao extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ten',  
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function hocPhans()
    {
        return $this->hasMany(HocPhan::class, 'id_bac_dao_tao');
    }   
}
