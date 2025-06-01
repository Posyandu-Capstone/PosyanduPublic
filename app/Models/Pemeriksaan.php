<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan';

    protected $fillable = [
        'user_id',
        'anggota_keluarga_nik',
        'antrian_id',
        'tipe_pemeriksaan',
        'catatan',
        'tindakan',
        'diagnosa',
        'tanggal_periksa',
        'berat_badan',
        'tinggi_badan',
        'bb_per_umur',
        'tb_per_umur',
        'bb_per_tb',
        // 'berita_id',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'user_id');
    }

    public function anggota_keluarga()
    {
        return $this->belongsTo(AnggotaKeluarga::class, 'anggota_keluarga_nik', 'nik');
    }

    public function antrian()
    {
        return $this->belongsTo(Antrian::class, 'antrian_id', 'id');
    }
    //     public function berita()
    // {
    //     return $this->belongsTo(Berita::class, 'berita_id', 'id');
    // }

    public function balita()
    {
        return $this->hasOne(PemeriksaanBalita::class, 'id', 'id');
    }
}
