<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warga;
use Faker\Factory as Faker;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $faker = Faker::create();

        for($i = 0; $i <= 30; $i++){
            Warga::create([
                'nama_lengkap' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => bcrypt("12345678"), 
            ]);
        }
    }
}