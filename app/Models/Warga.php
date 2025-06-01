<?php

namespace App\Models;
use App\Models\Keluarga;
use App\Models\Pemeriksaan;
use App\Models\AnggotaKeluarga;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class Warga extends Authenticatable implements JWTSubject {
    use HasFactory;

    protected $table = 'warga';
    protected $fillable = [
        'id',
        'keluarga_no_kk',
        'anggota_keluarga_nik',
        'nama_lengkap',
        'password',
        'email',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_telp',
        'Anak_Ke',
    ];

    protected $hidden = [
        'password',
    ];

    public function keluarga() {
        return $this->belongsTo(Keluarga::class);
    }

    // public function anggotaKeluarga() {
    //     return $this->hasManyThrough(AnggotaKeluarga::class, Keluarga::class, 'id', 'keluarga_id', 'keluarga_id', 'id');
    // }

    public function anggotaKeluarga()
    {
        return $this->hasOne(AnggotaKeluarga::class, 'nik', 'nik');
    }
    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'user_id');
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
