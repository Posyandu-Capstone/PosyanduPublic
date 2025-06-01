<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;

class AnggotaKeluarga extends Model implements JWTSubject
{
    use HasFactory;
    protected $primaryKey = 'nik';
     public $incrementing = false;       
    protected $keyType = 'string';
    protected $table = 'anggota_keluarga';
    protected $fillable = [
        'keluarga_no_kk',
        'nik',
        'nama_anggota_keluarga',
        'posisi_keluarga',
        'posko_terdaftar',
        'tanggal_lahir',
        'jenis_kelamin',
        'Anak_Ke',
        'tinggi_lahir',
        'berat_lahir',
        'penyakit_bawaan',
        'bpjs',
        'gakin',
        'nomor_telp',
    ];

    public function posyandu()
    {
        return $this->belongsTo(\App\Models\Posyandu::class, 'posko_terdaftar', 'nama_posyandu');
    }
    public function user()
    {
        return $this->hasOne(User::class);
    }
    

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_no_kk', 'no_kk');
    }

        public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'anggota_keluarga_nik', 'nik');
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
