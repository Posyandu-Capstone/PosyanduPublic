<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{AnggotaKeluarga, Keluarga, Posyandu};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfilAnggotaKeluargaController extends Controller
{
    public function updateAnggota($nik, Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'nama_anggota_keluarga' => 'nullable|string|max:255',
                'nik' => [
                    'nullable',
                    'string',
                    Rule::unique('anggota_keluarga', 'nik')->ignore($nik, 'nik'),
                ],
                'posisi_keluarga' => 'nullable|string|in:Istri,Anak,Kepala Keluarga',
                'anak_ke' => 'nullable|numeric',
                'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
                'tanggal_lahir' => 'nullable|date'
            ]);

            $anggota = AnggotaKeluarga::where('nik', $nik)->first();

            $anggota->update($request->only(
                'nama_anggota_keluarga',
                'nik',
                'posisi_keluarga',
                'anak_ke',
                'jenis_kelamin',
                'tanggal_lahir'
            ));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Profil anggota berhasil diperbarui.',
                'data' => $anggota
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data anggota: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetailAnggota($nik)
    {
        $anggota = AnggotaKeluarga::where('nik', $nik)->first();

        return response()->json($anggota);
    }

    public function getPosyandu($no_kk)
    {
        $keluarga = Keluarga::where('no_kk', $no_kk)->first();

        if (!$keluarga) {
            return response()->json(['message' => 'Keluarga tidak ditemukan'], 404);
        }

        $posyandu = Posyandu::where('id', $keluarga->posyandu_id)->first();

        if (!$posyandu) {
            return response()->json(['message' => 'Posyandu tidak ditemukan'], 404);
        }

        $jumlahKeluarga = Keluarga::where('posyandu_id', $posyandu->id)->count();

        return response()->json([
            'id' => $posyandu->id,
            'nama_posyandu' => $posyandu->nama_posyandu,
            'alamat' => $posyandu->alamat,
            'jumlah_keluarga' => $jumlahKeluarga,
        ]);
    }
}
