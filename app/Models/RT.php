<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    use HasFactory;
    protected $table = 'rt';
    protected $fillable = ['No_RT', 'rw_id'];

    public function rw()
    {
        return $this->belongsTo(RW::class, 'rw_id');
    }

    public function keluarga()
    {
        return $this->hasMany(Keluarga::class);
    }
}
