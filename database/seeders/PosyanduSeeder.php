<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PosyanduSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semuaDesa = Desa::all();
        $semuaKecamatan = Kecamatan::all();

        if ($semuaDesa->isEmpty() || $semuaKecamatan->isEmpty()) {
            $this->command->warn("Database kosong, seeder dibatalkan");
            return;
        }


        for ($i = 0; $i < 20; $i++) {
            $desa = $semuaDesa->random();
            $kecamatan = $semuaKecamatan->where('code', Str::substr($desa->code, 0, 8))->first();

            $namaPosyandu = ['Melati', 'Mawar', 'Anggrek', 'Kamboja', 'Dahlia'];
            $jenisPelayanan = ['Imunisasi', 'Timbang', 'Penyuluhan', 'Pemeriksaan Ibu'];
            $fasilitas = ['Timbangan', 'Ruang Tunggu', 'Alat Kesehatan', 'Poster Edukasi'];

            $posyandu = \App\Models\Posyandu::create([
                'nama_posyandu' => 'Posyandu ' . Arr::random($namaPosyandu) . " $i",
                'alamat' => 'Jl. Contoh ' . rand(0, 100),
                'desa_id' => $desa->code,
                'kecamatan_id' => $kecamatan ? $kecamatan->code : '35.07.18',
                'jenis_pelayanan' => json_encode(Arr::random($jenisPelayanan, rand(1, count($jenisPelayanan)))),
                'fasilitas' => json_encode(Arr::random($fasilitas, rand(1, count($fasilitas)))),
            ]);

        }
    }
}
