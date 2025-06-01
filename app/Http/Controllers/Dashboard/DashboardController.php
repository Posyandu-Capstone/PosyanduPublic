<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\AnggotaKeluarga;
use App\Models\Keluarga;
use App\Models\PemeriksaanBalita;
use App\Models\Posyandu;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getHariIni()
    {
        Carbon::setLocale('id');

        $hari = Carbon::now()->translatedFormat('l');
        $tanggal = Carbon::now()->translatedFormat('j F Y');

        return response()->json([
            'hari' => $hari,
            'tanggal' => $tanggal,
            'full' => "$hari, $tanggal"
        ]);
    }
    public function getPoskoPosyanduAll()
    {
        $posyandu = Posyandu::with('kader')->get()->map(function ($item) {
            $jumlahAnggota = AnggotaKeluarga::where('posko_terdaftar', $item->nama_posyandu)->count();

            return [
                'id' => $item->id,
                'nama_posyandu' => $item->nama_posyandu,
                'alamat' => $item->alamat,
                'nama_koordinator' => $item->kader?->nama_lengkap ?? 'Belum Ditentukan',
                'jumlah_anggota' => $jumlahAnggota,
            ];
        });

        return response()->json([
            'message' => 'Semua data posyandu berhasil didapatkan',
            'data' => $posyandu,
        ], 200);
    }
    public function getNamaPosyanduOnly()
    {
        $namaPosyandu = Posyandu::select('id','nama_posyandu')->get();

        return response()->json([
            'message' => 'Daftar nama posyandu berhasil didapatkan',
            'data' => $namaPosyandu,
        ], 200);
    }


    public function getAllowedPoskoPosyanduAll()
    {
        $user = Auth::user();

        if ($user->posisi === 'Admin Desa') {
            $posyandus = Posyandu::select('id', 'nama_posyandu')->get();
        } elseif ($user->posisi === 'Kader') {
            $posyandus = $user->posyandus->pluck('nama_posyandu', 'id');
            $posyandus = $posyandus->map(function ($nama, $id) {
                return ['id' => $id, 'nama_posyandu' => $nama];
            })->values();
        } else {
            return response()->json(['message' => 'Role tidak diizinkan'], 403);
        }

        return response()->json([
            'message' => 'Data posyandu berhasil didapatkan',
            'data' => $posyandus,
        ], 200);
    }

    public function getStatistics()
    {
        $today = Carbon::today();

        $data = [
            'balita' => AnggotaKeluarga::whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) < 5", [$today])->count(),
            'remaja' => AnggotaKeluarga::whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 5 AND 17", [$today])->count(),
            'dewasa' => AnggotaKeluarga::whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 17 AND 40", [$today])->count(),
            'lansia' => AnggotaKeluarga::whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) > 40", [$today])->count(),
        ];

        return response()->json([
            'message' => 'Data statistik warga berhasil diambil',
            'data' => $data
        ], 200);
    }

    public function getDataBalita()
    {
        return response()->json([
            'berat_badan' => $this->kategoriTekstual('bb_u'),
            'tinggi_badan' => $this->kategoriTekstual('tb_u'),
            'gizi' => $this->kategoriTekstual('bb_tb'),
        ]);
    }

    public function kategoriTekstual(string $request)
    {
        $data = PemeriksaanBalita::select($request)->get();

        $kategori = [
            'Kurang' => 0,
            'Normal' => 0,
            'Lebih' => 0
        ];

        foreach ($data as $item) {
            $value = $item->$request;

            if ($value == 'Kurang') {
                $kategori['Kurang']++;
            } elseif ($value == 'Normal') {
                $kategori['Normal']++;
            } else {
                $kategori['Lebih']++;
            }
        }

        return $kategori;
    }

    public function getSearchPosyandu(Request $request)
    {
        $search = $request->query('search');

        $query = Posyandu::query();

        if ($search) {
            $query->where('nama_posyandu', 'like', "%$search%");
        }

        $posyandu = $query->get();

        return response()->json($posyandu);
    }


    public function getPosyanduList()
    {
        $posyandu = Posyandu::with('desa')
            ->get()
            ->map(function ($item) {
                return [
                    'nama_posyandu' => $item->nama_posyandu,
                    'nama_desa' => optional($item->desa)->Nama_Desa
                ];
            });

        return response()->json([
            'message' => 'Daftar posyandu berhasil diambil',
            'data' => $posyandu
        ], 200);
    }
}
