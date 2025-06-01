<?php

namespace Database\Seeders;

use App\Models\AnggotaKeluarga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Keluarga;

class AnggotaKeluargaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $noKKList = DB::table('keluarga')->pluck('no_kk');

        // Track Kepala Keluarga dan Istri yang sudah ada per KK
        $kkStatus = [];

        foreach ($noKKList as $noKK) {
            $kkStatus[$noKK] = [
                'kepala_keluarga' => false,
                'istri' => false,
            ];
        }

        $jumlahData = 40;
        for ($i = 0; $i < $jumlahData; $i++) {
            $randomNoKK = $noKKList->random();
            $status = &$kkStatus[$randomNoKK];

            if (!$status['kepala_keluarga']) {
                $posisi = 'Kepala Keluarga';
                $status['kepala_keluarga'] = true;
            } elseif (!$status['istri']) {
                $posisi = 'Istri';
                $status['istri'] = true;
            } else {
                $posisi = 'Anak';
            }

            $nama = $faker->name();

            $posyanduId = DB::table('keluarga')->where('no_kk', $randomNoKK)->value('posyandu_id');

            $namaPosyandu = DB::table('posyandu')->where('id', $posyanduId)->value('nama_posyandu');

            DB::table('anggota_keluarga')->insert([
                'nik' => '3525' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'keluarga_no_kk' => $randomNoKK,
                'nama_anggota_keluarga' => $nama,
                'nomor_telp' => $faker->phoneNumber(),
                'posisi_keluarga' => $posisi,
                'posko_terdaftar' => $namaPosyandu ?? 'Posyandu Tidak Diketahui',
                'tanggal_lahir' => $faker->date('Y-m-d'),
                'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
                'penyakit_bawaan' => $faker->randomElement([
                    'Asma',
                    'Diabetes',
                    'Hipertensi',
                    'Jantung',
                    null,
                    null,
                    null
                ]),
                'tinggi_lahir' => rand(30, 50),
                'berat_lahir' => rand(20, 50) / 10,
                'bpjs' => (bool)rand(0, 1),
                'gakin' => (bool)rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($posisi === 'Kepala Keluarga') {
                Keluarga::where('no_kk', $randomNoKK)->update([
                    'nama_ayah' => $nama
                ]);
            } elseif ($posisi === 'Istri') {
                Keluarga::where('no_kk', $randomNoKK)->update([
                    'nama_ibu' => $nama
                ]);
            }
        }

        $adminNoKK = $noKKList->random();
        $adminPosyanduId = DB::table('keluarga')->where('no_kk', $adminNoKK)->value('posyandu_id');
        $adminNamaPosyandu = DB::table('posyandu')->where('id', $adminPosyanduId)->value('nama_posyandu');

        AnggotaKeluarga::create([
            'nama_anggota_keluarga' => 'admin2_lengkap',
            'keluarga_no_kk' => $adminNoKK,
            'posisi_keluarga' => "Kepala Keluarga",
            'nik' => '3525102301040003',
            'posko_terdaftar' => $adminNamaPosyandu ?? 'Posyandu Tidak Diketahui',
            'nomor_telp' => $faker->phoneNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
