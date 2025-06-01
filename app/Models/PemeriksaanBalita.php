<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanBalita extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_balita';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'pemeriksaan_id',
        'imunisasi',
        'berat_badan_kg',
        'tinggi_badan',
        'tanggal_timbang',
        'PMT',
        'total_PMT',
        'ASI',
        'vit',
        'PM',
        'NTO_B',
        'Suplemen',
        'zscore_tb_u',
        'zscore_bb_u',
        'zscore_bb_tb',
        'tb_u',
        'bb_u',
        'bb_tb'
    ];

    protected $casts = [
        'ASI' => 'boolean',
        'tinggi_badan' => 'float',
        'berat_badan_kg' => 'float',
        'total_PMT' => 'float',
    ];  
    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class, 'id');
    }
}
