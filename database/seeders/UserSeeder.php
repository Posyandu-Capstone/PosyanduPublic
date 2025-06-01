<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nik = substr(preg_replace('/\D/', '', Str::random(20)), 0, 16);
        $nik = str_pad($nik, 16, '0', STR_PAD_RIGHT);

        for ($i = 0; $i < 10; $i++) {

            User::create([
                'nama_lengkap' => fake()->name(),
                'email' => "kader$i@example.com",
                'nik' => $nik,
                'password' => Hash::make('password123'), 
                'posisi' => 'Kader',
                'status' => 'success',
                'kontak' => '08' . rand(1000000000, 9999999999),
            ]);
        }
        //buat test
        User::create([
            'nama_lengkap' => 'admin2_lengkap',
            'email' => "admin2@gmail.com",
            'nik' => '3525102301040003',
            'password' => Hash::make('password123'),
            'posisi' => 'Admin Desa',
            'status' => 'success',
            'kontak' => '08' . rand(1000000000, 9999999999),
        ]);
        User::create([
            'nama_lengkap' => 'admin3_lengkap',
            'email' => "admin3@gmail.com",
            'nik' => '3525102301040004',
            'password' => Hash::make('password123'),
            'posisi' => 'Admin Verifikator',
            'status' => 'success',
            'kontak' => '08' . rand(1000000000, 9999999999),
        ]);
    }
}
