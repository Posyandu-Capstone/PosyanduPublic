<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemeriksaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua ID warga yang tersedia
        $wargaIds = DB::table('warga')->pluck('id');

        foreach ($wargaIds as $id) {
            DB::table('pemeriksaan')->insert([
                'warga_id' => $id,
                'tanggal_periksa' => Carbon::now()->subDays(rand(1, 365)),
                'berat_badan' => rand(30, 70) + (rand(0, 9) / 10),
                'tinggi_badan' => rand(130, 180) + (rand(0, 9) / 10),
                'bb_per_umur' => collect(['Kurang', 'Normal', 'Lebih'])->random(),
                'tb_per_umur' => collect(['TB Pendek', 'TB Normal', 'TB Tinggi'])->random(),
                'bb_per_tb' => collect(['Gizi Buruk', 'Gizi Kurang', 'Gizi Baik', 'Gizi Lebih'])->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
