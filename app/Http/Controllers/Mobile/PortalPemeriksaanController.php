<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Pemeriksaan, PemeriksaanBalita, AnggotaKeluarga, Antrian, Berita, Posyandu, Keluarga};

class PortalPemeriksaanController extends Controller
{
    public function anggota($keluarga_no_kk)
    {
        $anggota = AnggotaKeluarga::where('keluarga_no_kk', $keluarga_no_kk)
            ->get()
            ->map(function ($item) {
                return [
                    'nama_anggota_keluarga' => $item->nama_anggota_keluarga,
                    'posisi_keluarga' => $item->posisi_keluarga,
                    'anggota_keluarga_nik' => $item->nik
                ];
            });

        if ($anggota->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data pemeriksaan untuk NIK ini.'
            ], 404);
        }

        return response()->json($anggota);
    }

    public function show($wargaId, $anggota_nik, $tipePemeriksaan)
    {
        if ($tipePemeriksaan == "Anak") {
            $tipePemeriksaan = "balita";
        }

        $pemeriksaanList = Pemeriksaan::with(['antrian.berita', 'balita'])
            ->where('user_id', $wargaId)
            ->where('anggota_keluarga_nik', $anggota_nik)
            ->where('tipe_pemeriksaan', $tipePemeriksaan)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($pemeriksaanList->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data pemeriksaan untuk NIK ini.'
            ], 404);
        }

        $riwayat = $pemeriksaanList->map(function ($item) {
            $pemeriksaanBalitaId = PemeriksaanBalita::where('pemeriksaan_id', $item->id)->value('id');

            return [
                'id' => $pemeriksaanBalitaId,
                'tanggal' => $item->updated_at->format('d M Y'),
                'lokasi_pelaksanaan' => optional($item->antrian->berita)->lokasi_pelaksanaan,
                'judul_berita' => optional($item->antrian->berita)->kategori
            ];
        });

        $nama = AnggotaKeluarga::where('nik', $anggota_nik)->value('nama_anggota_keluarga');

        return response()->json([
            'riwayat' => $riwayat,
            'nama_anggota' => $nama
        ]);
    }

    public function get($id)
    {
        $pemeriksaantable = Pemeriksaan::with('balita')
            ->where('id', $id)
            ->first();
        
        $pemeriksaan = PemeriksaanBalita::where('pemeriksaan_id', $pemeriksaantable->id)->first();

        $nama = AnggotaKeluarga::where('nik', $pemeriksaantable->anggota_keluarga_nik)->first();

        // $posyanduId = Keluarga::where('no_kk', $nama->keluarga_no_kk)->first();
        // $namaPosyandu = Posyandu::find($posyanduId->id);
        // $alamatPosyandu = $namaPosyandu->alamat;

        return response()->json([
            'nama_pasien' => $nama->nama_anggota_keluarga,
            // 'posyandu' => $namaPosyandu->nama_posyandu,
            // 'alamat_posyandu' => $alamatPosyandu,
            'tinggi_badan' => $pemeriksaan->tinggi_badan,
            'berat_badan' => $pemeriksaan->berat_badan_kg,
            'PMT' => $pemeriksaan->PMT,
            'total_PMT' => $pemeriksaan->total_PMT,
            'ASI' => $pemeriksaan->ASI,
            'vit' => $pemeriksaan->vit,
            'imunisasi' => $pemeriksaan->imunisasi
        ]);
    }
}