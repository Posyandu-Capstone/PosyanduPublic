<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Kecamatan;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::get('https://wilayah.id/api/districts/35.07.json');

        if($response->successful()) {
            $dataKecamatan = $response->json()['data'];

            foreach ($dataKecamatan as $kecamatan) {
                Kecamatan::create([
                    'code' => $kecamatan['code'],
                    'Nama_Kecamatan' => $kecamatan['name']
                ]);
            }
            
            $this->command->info("Data berhasil dimasukkan");
        } else {
            $this->command->info("Gagal memasukkan data dari API");
        }
    }
}
