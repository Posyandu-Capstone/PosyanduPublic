<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Posyandu;

class PosyanduUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('posisi', 'Kader')->get();
        $posyandus = Posyandu::all();

        foreach ($users as $user) {
            $randomPosyandus = $posyandus->random(rand(1, 2));

            foreach ($randomPosyandus as $posyandu) {
                DB::table('posyandu_user')->insert([
                    'user_id' => $user->id,
                    'posyandu_id' => $posyandu->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}