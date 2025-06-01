<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // php artisan migrate:fresh --seed 
    public function run(): void
    {
        $this->call([
            KecamatanSeeder::class,
            DesaSeeder::class,
            DusunSeeder::class,
            RWSeeder::class,
            RTSeeder::class,
            UserSeeder::class,
            PosyanduSeeder::class,
            PosyanduUserSeeder::class,
            KeluargaSeeder::class,
            AnggotaKeluargaSeeder::class,
            BeritaSeeder::class,
            PemeriksaanBalitaSeeder::class,
        ]);
    }
}
