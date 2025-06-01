<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\{Keluarga, AnggotaKeluarga, Pemeriksaan, PemeriksaanBalita, Antrian, Warga};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PortalBeritaController extends Controller
{
    public function getBerita($no_kk)
    {
        $posyanduId = Keluarga::where('no_kk', $no_kk)->value('posyandu_id');

        $beritaList = Berita::where('posyandu_id', $posyanduId)->get(['id', 'judul', 'tanggal', 'tempat']);

        if ($beritaList->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada berita untuk saat ini.'
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data berita',
            'data' => $beritaList
        ]);
    }

    public function getDetail($id)
    {
        $berita = Berita::where('id', $id)->first();

        return response()->json([
            'message' => 'Berhasil mengambil data berita',
            'data' => $berita
        ]);
    }

    public function getNomorAntrianBerikutnya($beritaId)
    {
        $nomorTerakhir = Antrian::where('berita_id', $beritaId)->max('nomor_antrian');

        $nomorBerikutnya = $nomorTerakhir ? $nomorTerakhir + 1 : 1;

        return response()->json(["nomor" => $nomorBerikutnya]);
    }

    public function getAnggota($no_kk)
    {
        $anggota = AnggotaKeluarga::where('keluarga_no_kk', $no_kk)->get();

        if ($anggota->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada berita untuk saat ini.'
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data anggota',
            'data' => $anggota
        ]);
    }

    public function daftarAntrian(Request $request)
    {
        $request->validate([
            'warga_nik' => 'required|exists:warga,anggota_keluarga_nik',
            'nik' => 'required|exists:anggota_keluarga,nik',
            'berita_id' => 'required|exists:berita,id',
            'tipe_pemeriksaan' => 'required|in:balita',
        ]);

        return DB::transaction(function () use ($request) {
            $nik = $request->nik;
            $wargaNik = $request->warga_nik;
            $beritaId = $request->berita_id;
            $tipePemeriksaan = $request->tipe_pemeriksaan;

            $sudahTerdaftar = Pemeriksaan::where('anggota_keluarga_nik', $nik)
                ->whereHas('antrian', function ($query) use ($beritaId) {
                    $query->where('berita_id', $beritaId);
                })->exists();

            if ($sudahTerdaftar) {
                return response()->json(['message' => 'Anggota sudah terdaftar di antrian ini'], 404);
            }

            $nomorAntrianTerakhir = Antrian::where('berita_id', $beritaId)->max('nomor_antrian');
            $nomorBaru = $nomorAntrianTerakhir ? $nomorAntrianTerakhir + 1 : 1;

            $user = Warga::where('anggota_keluarga_nik', $wargaNik)->firstOrFail();

            $antrian = Antrian::create([
                'berita_id' => $beritaId,
                'nomor_antrian' => $nomorBaru,
                'created_at' => now(),
            ]);

            $pemeriksaan = Pemeriksaan::create([
                'user_id' => $user->id,
                'antrian_id' => $antrian->id,
                'anggota_keluarga_nik' => $nik,
                'tipe_pemeriksaan' => $tipePemeriksaan,
                'created_at' => now(),
            ]);

            if ($tipePemeriksaan === "balita") {
                PemeriksaanBalita::create([
                    'pemeriksaan_id' => $pemeriksaan->id
                ]);
            } else {
                return response()->json(['message' => 'Untuk sekarang hanya ada Balita'], 409);
            }

            return response()->json([
                'message' => 'Pendaftaran berhasil',
                'nomor_antrian' => $nomorBaru,
                'pemeriksaan_id' => $pemeriksaan->id
            ]);
        });
    }


    public function getAnggotaAntrian($berita_id, $wargaId)
    {
        $anggota = Pemeriksaan::with('antrian', 'anggota_keluarga')
            ->where('user_id', $wargaId)
            ->whereHas('antrian', function ($query) use ($berita_id) {
                $query->where('berita_id', $berita_id);
            })
            ->get();

        if ($anggota->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada anggota yang terdaftar',
                'data' => []
            ], 200);
        }

        $data = $anggota->map(function ($item) {
            return [
                'nama' => $item->anggota_keluarga?->nama_anggota_keluarga,
                'posisi' => $item->anggota_keluarga?->posisi_keluarga,
                'nomor_antrian' => $item->antrian?->nomor_antrian
            ];
        });

        return response()->json([
            'message' => 'Berhasil mengambil data anggota',
            'data' => $data
        ]);
    }
}
