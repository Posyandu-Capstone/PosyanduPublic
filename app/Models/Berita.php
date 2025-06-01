<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;
    protected $table = 'berita';
    protected $fillable = ['posyandu_id', 'judul', 'deskripsi', 'kategori', 'tempat', 'waktu', 'tanggal'];

    public function antrian()
    {
        return $this->hasMany(Antrian::class, 'berita_id', 'id');
    }
    public function userBerita()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
