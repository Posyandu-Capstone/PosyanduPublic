<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;
    protected $table = 'desa';

    protected $fillable = ['Nama_Desa', 'kecamatan_id', 'code'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function dusun()
    {
        return $this->hasMany(Dusun::class);
    }
}
