<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Keluarga, RT, RW, Posyandu};

class KeluargaSeeder extends Seeder
{

    public function run(): void
    {
        $rtList = RT::all();
        $dusunNames = DB::table('dusun')->pluck('Nama_Dusun');
        for ($i = 0; $i < 20; $i++) {
            $rt = $rtList->random();
            $rw = RW::where('code', $rt->rw_id)->first();
            $posyandu = Posyandu::inRandomOrder()->first();

            $lokasiLengkap = 'No_RT' . str_pad($rt->No_RT, 2, '0', STR_PAD_LEFT) . '/No_RW' . str_pad($rw->No_RW, 2, '0', STR_PAD_LEFT);
            // $lokasiLengkap = 'No_RT' . str_pad($rt->No_RT, 2, '0', STR_PAD_LEFT).'/code_' . str_pad($rt->code, 2, '0', STR_PAD_LEFT) . '/No_' . str_pad($rw->No_RW, 2, '0', STR_PAD_LEFT).'/code_RW' . str_pad($rw->code, 2, '0', STR_PAD_LEFT);
            $namaDusun = $dusunNames->random();
            Keluarga::create([
                'no_kk' => '31720' . rand(100000000, 999999999),
                'posyandu_id' => $posyandu->id,
                'lokasi_lengkap' => $rt->code,
                'alamat' => 'Jl. Mawar No. ' . $i . ' | ' . $lokasiLengkap,
                'nama_dusun' => $namaDusun,
            ]);
        }

        DB::table('keluarga')->insert([
            'no_kk' => '1234567891234567',
            'posyandu_id' => Posyandu::inRandomOrder()->first()->id,
            'lokasi_lengkap' => 'RT023',
            'alamat' => 'Jl. Mawar No. 21 | RT023/RW003',
            'nama_dusun' => $dusunNames->random(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
