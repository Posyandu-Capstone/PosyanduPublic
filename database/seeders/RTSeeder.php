<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Rw, RT};

class RTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rwList = RW::all();
        $counter = 1;

        foreach ($rwList as $rw) {
            for ($i = 1; $i <= 3; $i++) {
                RT::create([
                    'code' => 'RT' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                    'rw_id' => $rw->code,
                    'No_RT' => $i
                ]);
                $counter++;
            }
        }
    }
}
