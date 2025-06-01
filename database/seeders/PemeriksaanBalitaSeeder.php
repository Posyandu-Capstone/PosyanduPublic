<?php

namespace Database\Seeders;

use App\Models\{Keluarga, AnggotaKeluarga, Warga, Balita, Antrian, Pemeriksaan, PemeriksaanBalita, Berita};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class PemeriksaanBalitaSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();
        $namaPosyandu = ['Melati', 'Mawar', 'Anggrek', 'Kamboja', 'Dahlia'];
        // Seeder awal: isi data dummy keluarga, anggota keluarga, balita, warga
        for ($i = 0; $i < 20; $i++) {
            $keluarga = Keluarga::inRandomOrder()->first();

            $anggota = AnggotaKeluarga::create([
                'nik' => fake()->unique()->numerify('###############'),
                'keluarga_no_kk' => $keluarga->no_kk,
                'nama_anggota_keluarga' => fake()->name(),
                'posisi_keluarga' => 'Anak',
                'tanggal_lahir' => fake()->date('Y-m-d', '-1 years'),
                'jenis_kelamin' => fake()->randomElement(['Laki-laki', 'Perempuan']),
                'Anak_Ke' => fake()->numberBetween(1, 4),
                // 'posko_terdaftar' => 'Posyandu ' . Arr::random($namaPosyandu) . " $i", //Silahkan kalau butuh
                'penyakit_bawaan' => fake()->randomElement(['Asma', 'Diabetes', 'Hipertensi', 'Jantung', null, null, null]),
                'tinggi_lahir' => rand(30, 50),
                'berat_lahir' => rand(20, 50) / 10,
                'bpjs' => (bool)rand(0, 1),
                'gakin' => (bool)rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Balita::create([
                'anggota_keluarga_nik' => $anggota->nik
            ]);

            Warga::create([
                'anggota_keluarga_nik' => $anggota->nik,
                'keluarga_no_kk' => $keluarga->no_kk,
                'nama_lengkap' => $anggota->nama_anggota_keluarga,
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'no_telp' => fake()->phoneNumber(),
            ]);
        }

        for ($i = 0; $i < 20; $i++) {
            $anggotaList = AnggotaKeluarga::whereRaw("TIMESTAMPDIFF(MONTH, tanggal_lahir, ?) < 60", [$today])->pluck('nik');

            if ($anggotaList->isEmpty()) {
                continue;
            }

            $anggotaNIK = $anggotaList->random();
            $anggota = AnggotaKeluarga::where('nik', $anggotaNIK)->first();

            $warga = Warga::where('anggota_keluarga_nik', $anggotaNIK)->first();
            if (!$warga) {
                continue;
            }

            $berita = Berita::inRandomOrder()->first();
            $jumlah_antrian = Antrian::where('berita_id', $berita->id)->count();
            $nomor_antrian = $jumlah_antrian + 1;

            $antrian = Antrian::create([
                'nomor_antrian' => $nomor_antrian,
                'berita_id' => $berita->id
            ]);

            $pemeriksaan = Pemeriksaan::create([
                'user_id' => $warga->id,
                'anggota_keluarga_nik' => $anggotaNIK,
                'antrian_id' => $antrian->id,
                'tipe_pemeriksaan' => 'balita',
                'catatan' => 'Catatan ke-' . ($i + 1),
                'diagnosa' => 'Diagnosa dummy',
                'tindakan' => 'Tindakan dummy',
                'created_at' => now()
            ]);

            PemeriksaanBalita::create([
                'pemeriksaan_id' => $pemeriksaan->id,
                'imunisasi' => fake()->randomElement(['Polio', 'Campak', 'Hepatitis B', 'BCG']),
                'berat_badan_kg' => rand(60, 130) / 10,
                'tinggi_badan' => rand(60, 90),
                'tanggal_timbang' => now()->subDays(rand(0, 30)),
                'PMT' => fake()->randomElement(['Bubur ayam', 'Telur rebus', 'Nasi tim', 'Biskuit bayi']),
                'total_PMT' => rand(5000, 25000),
                'ASI' => (bool)rand(0, 1),
                'vit' => fake()->randomElement(['A', 'B', 'C', 'D']),
                'PM' => fake()->randomElement(['Susu', 'MPASI', 'Air putih']),
                'NTO_B' => fake()->randomElement(['Ya', 'Tidak']),
                'Suplemen' => fake()->randomElement(['Darah', 'Creatine', 'Herbal', "Whey Protein"]),
                'zscore_tb_u' => round(rand(-20, 20) / 10, 1),
                'zscore_bb_u' => round(rand(-20, 20) / 10, 1),
                'zscore_bb_tb' => round(rand(-20, 20) / 10, 1),
                'tb_u' => fake()->randomElement(['Kurang', 'Normal', 'Lebih']),
                'bb_u' => fake()->randomElement(['Kurang', 'Normal', 'Lebih']),
                'bb_tb' => fake()->randomElement(['Kurang', 'Normal', 'Lebih']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
