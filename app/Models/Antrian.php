<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrian';
    protected $fillable = [
        'nomor_antrian',
        'berita_id'
    ];

    public function pemeriksaan()
    {
        return $this->hasOne(Pemeriksaan::class, 'antrian_id', 'id');
    }

    public function berita()
    {
        return $this->belongsTo(Berita::class, 'berita_id', 'id');
    }
}
