<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    use HasFactory;
    protected $table = 'posyandu';

    // protected $fillable = ['nama_posyandu', 'desa_id', 'users_id', 'jumlah_anggota'];
    protected $fillable = ['nama_posyandu', 'desa_id', 'kecamatan_id', 'alamat'];

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id', 'code');
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'posyandu_user');
    }

    public function anggotaKeluarga()
    {
        return $this->hasMany(\App\Models\AnggotaKeluarga::class, 'posko_terdaftar', 'nama_posyandu');
    }

    public function keluargas()
    {
        return $this->hasMany(Keluarga::class, 'posyandu_id');
    }
}
