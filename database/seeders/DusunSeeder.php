<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dusun;

class DusunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $desaId = '35.07.18.2001';
        for ($i = 0; $i <= 20; $i++) {
            Dusun::create([
                'code' => 'DUS' . str_pad($i, 3, '0', STR_PAD_LEFT), 
                'desa_id' => $desaId,
                'Nama_Dusun' => 'Dusun ' . chr(64 + $i),
            ]);
        }
    }
}
