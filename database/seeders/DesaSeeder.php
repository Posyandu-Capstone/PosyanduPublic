<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Desa;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::get('https://wilayah.id/api/villages/35.07.18.json');

        if($response->successful()) {
            $dataDesa = $response->json()['data'];

            foreach ($dataDesa as $desa) {
                Desa::create([
                    'code' => $desa['code'],
                    'kecamatan_id' => "35.07.18",
                    'Nama_Desa' => $desa['name']
                ]);
            }
            
            $this->command->info("Data berhasil dimasukkan");
        } else {
            $this->command->info("Gagal memasukkan data dari API");
        }
    }
}
