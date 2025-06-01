<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Dusun, Rw};


class RWSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dusunList = Dusun::all();
        $counter = 1;

        foreach ($dusunList as $dusun) {
            for ($i = 1; $i <= 2; $i++) {
                RW::create([
                    'code' => 'RW' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                    'dusun_id' => $dusun->code,
                    'No_RW' => $i
                ]);
                $counter++;
            }
        }
    }
}
