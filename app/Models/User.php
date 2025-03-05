<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'sdt',
        'email',
        'password',
        'dia_chi',
        'ngay_sinh',
        'gioi_tinh',
        'id_bo_mon',
        'id_chuc_vu',
        'able'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function boMon()
    {
        return $this->belongsTo(BoMon::class, 'id_bo_mon');
    }

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'id_chuc_vu');
    }

    public function ctDSDangKies()
    {
        return $this->hasMany(CTDSDangKy::class, 'id_vien_chuc');
    }

    public function dsHops()
    {
        return $this->hasMany(DSHop::class, 'id_vien_chuc');
    }
}
