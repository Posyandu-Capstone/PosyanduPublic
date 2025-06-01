<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rw extends Model
{
    use HasFactory;
    protected $table = 'rw';

    protected $fillable = ['No_RW', 'dusun_id'];

    public function dusun()
    {
        return $this->belongsTo(Dusun::class);
    }

    public function rt()
    {
        return $this->hasMany(RT::class);
    }
}
