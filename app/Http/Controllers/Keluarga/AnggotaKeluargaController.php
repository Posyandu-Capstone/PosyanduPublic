<?php

namespace App\Http\Controllers\Keluarga;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AnggotaKeluargaController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'keluarga_no_kk' => 'required|string|max:255',
                'nik' => 'required|string|max:255|unique:anggota_keluarga,nik',
                'nama_anggota_keluarga' => 'required|string|max:255',
                'posisi_keluarga' => 'required|string|max:255',
            ]);

            $validatedData = $request->only(['keluarga_no_kk', 'nik', 'nama_anggota_keluarga', 'posisi_keluarga']);
            $validatedData['nik'] = htmlspecialchars($validatedData['nik'], ENT_QUOTES, 'UTF-8');
            $anggota = AnggotaKeluarga::create($validatedData);

            return response()->json(['Message' => 'Berhasil menambahkan data Keluarga', 'data' => $anggota], 201);
        } catch (Exception $e) {
            Log::error('Error storing anggota keluarga: ' . $e->getMessage());
            return response()->json(['Message' => 'Terjadi kesalahan saat menambahkan data Keluarga.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $nik)
    {
        try {
            $request->validate([
                'keluarga_no_kk' => 'required|string|max:255',
                'nama_anggota_keluarga' => 'required|string|max:255',
                'posisi_keluarga' => 'required|string|max:255',
                'posko_terdaftar' => 'required|string|max:255',
            ]);

            $validatedData = $request->only(['keluarga_no_kk', 'nama_anggota_keluarga', 'posisi_keluarga', 'posko_terdaftar']);

            $anggota = AnggotaKeluarga::findOrFail($nik);
            $anggota->update($validatedData);

            return response()->json(['Message' => 'Berhasil memperbarui data keluarga', 'data' => $anggota], 200);
        } catch (Exception $e) {
            Log::error('Error updating anggota keluarga: ' . $e->getMessage());
            return response()->json(['Message' => 'Terjadi kesalahan saat memperbarui data Keluarga.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($nik)
    {
        try {
            $anggota = AnggotaKeluarga::findOrFail($nik);
            return response()->json(['Message' => 'Data anggota keluarga berhasil didapatkan', 'data' => $anggota], 200);
        } catch (Exception $e) {
            Log::error('Error fetching anggota keluarga: ' . $e->getMessage());
            return response()->json(['Message' => 'Data anggota keluarga tidak ditemukan', 'error' => $e->getMessage()], 404);
        }
    }
}
