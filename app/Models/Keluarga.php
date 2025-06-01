<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Keluarga extends Model implements JWTSubject
{
    use HasFactory;

    protected $table = 'keluarga';
    protected $fillable = [
        'no_kk', 
        'posyandu_id',
        'alamat', 
        'lokasi_lengkap', 
        'nama_dusun', 
        'nama_ayah', 
        'nama_ibu', 
        'rt_rw',
        'posyandu_id',  
    ];

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    public function anggotaKeluarga()
    {
        return $this->hasMany(AnggotaKeluarga::class,  'keluarga_no_kk', 'no_kk');
    }

    public function rt()
    {
        return $this->hasOne(RT::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
