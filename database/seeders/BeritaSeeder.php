<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Berita;
use App\Models\Posyandu;
use Faker\Factory as Faker;

class BeritaSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk berita.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create();
        $categories = ['Technology', 'Health', 'Education', 'Sports', 'Politics'];

        for ($i = 0; $i < 20; $i++) {
            $posyandu = Posyandu::inRandomOrder()->first();
            // Membuat format waktu seperti "09:00 - 12:00 WIB"
            $startTime = $faker->time('H:i');
            $endTime = $faker->time('H:i');
            $waktu = $startTime . ' - ' . $endTime . ' WIB';

            Berita::create([
                'judul' => $faker->sentence(6),
                'deskripsi' => $faker->paragraph(2),
                'kategori' => $faker->randomElement($categories),
                'tempat' => $faker->city(),
                'waktu' => $waktu,
                'tanggal' => $faker->date('Y-m-d'),
                'posyandu_id' => $posyandu->id
            ]);
        }
    }
}
