<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\{Pemeriksaan, PemeriksaanBalita};
use App\Models\Warga;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PortalEKmsController extends Controller
{
    public function getData($nik, $definisi)
    {
        $pemeriksaanList = Pemeriksaan::where('anggota_keluarga_nik', $nik)
            ->with(['balita', 'anggota_keluarga'])
            ->get();

        if ($pemeriksaanList->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data pemeriksaan untuk NIK ini.'
            ], 404);
        }

        $riwayat = $pemeriksaanList->map(function ($item) use ($definisi) {
            $balita = $item->balita;
            $anggota = $item->anggota_keluarga;

            $umur = floor(\Carbon\Carbon::parse($anggota->tanggal_lahir)->diffInMonths($item->updated_at));

            $data = [
                'umur' => $umur . ' bulan',
            ];

            if ($definisi === 'TinggiBadan') {
                $data['tinggi_badan'] = $item->tinggi_badan ?? $balita?->tinggi_badan;
                $data['status_gizi'] = $balita?->tb_u;
            } else if ($definisi === 'BeratBadan') {
                $data['berat_badan'] = $item->berat_badan ?? $balita?->berat_badan_kg;
                $data['status_gizi'] = $balita?->bb_u;
            } else if ($definisi === 'Imunisasi') {
                $data['imunisasi'] = $item->imunisasi ?? $balita?->imunisasi;
                $data['tanggal'] = $balita?->tanggal_timbang;
            } else if ($definisi === 'Vit') {
                $data['vitamin'] = $item->vit ?? $balita?->vit;
                $data['tanggal'] = $balita?->tanggal_timbang;
            } else if ($definisi === 'Suplement') {
                $data['suplemen'] = $item->vit ?? $balita?->suplement;
                $data['tanggal'] = $balita?->tanggal_timbang;
            }

            return $data;
        });

        return response()->json([
            'data' => $riwayat
        ]);
    }

    public function getPerkembangan($nik) {
        $pemeriksaanList = Pemeriksaan::where('anggota_keluarga_nik', $nik)
            ->with(['balita', 'anggota_keluarga'])
            ->get();

        if ($pemeriksaanList->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data pemeriksaan untuk NIK ini.'
            ], 404);
        }

        $riwayat = $pemeriksaanList->map(function ($item)  {
            $balita = $item->balita;
            $anggota = $item->anggota_keluarga;

            $umur = floor(\Carbon\Carbon::parse($anggota->tanggal_lahir)->diffInMonths($item->updated_at));

            $data = [
                'tanggal' => $balita?->tanggal_timbang,
                'umur' => $umur . ' bulan',
                'tinggi_badan' => $item->tinggi_badan ?? $balita?->tinggi_badan,
                'berat_badan' => $item->berat_badan ?? $balita?->berat_badan_kg,

            ];

            return $data;
        });

        return response()->json([
            'data' => $riwayat
        ]);
    }

    public function getDataDiri($nik){
        $pemeriksaan = Pemeriksaan::where('anggota_keluarga_nik', $nik)
            ->with(['balita', 'anggota_keluarga'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$pemeriksaan) {
            return response()->json([
                'message' => 'Tidak ada data pemeriksaan untuk NIK ini.'
            ], 404);
        }

        $umur = floor(\Carbon\Carbon::parse($pemeriksaan->anggota_keluarga->tanggal_lahir)->diffInMonths(Carbon::now()));


        $data = [
            'nama' => $pemeriksaan->anggota_keluarga->nama_anggota_keluarga,
            'umur' => $umur . ' bulan',
            'berat_badan' => $pemeriksaan->balita->berat_badan_kg,
            'kategori' => $pemeriksaan->tipe_pemeriksaan
        ];

        return response()->json($data);
    }
}