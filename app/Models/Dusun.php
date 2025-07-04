<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dusun extends Model
{
    use HasFactory;
    protected $table = 'dusun';

    protected $fillable = ['Nama_Dusun', 'desa_id'];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function rw()
    {
        return $this->hasMany(RW::class);
    }
}
