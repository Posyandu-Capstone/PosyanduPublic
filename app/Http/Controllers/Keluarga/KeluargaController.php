<?php

namespace App\Http\Controllers\Keluarga;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class KeluargaController extends Controller
{
    // public function store(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'judul' => 'required|string|max:255',
    //             'description' => 'required|string|max:255',
    //         ]);

    //         $validatedData = $request->only(['judul', 'description']);
    //         $validatedData['judul'] = htmlspecialchars($validatedData['judul'], ENT_QUOTES, 'UTF-8'); // Sanitasi input judul
    //         $validatedData['description'] = htmlspecialchars($validatedData['description'], ENT_QUOTES, 'UTF-8'); // Sanitasi input description

    //         $keluarga = Keluarga::create($validatedData);

    //         return response()->json(['Message' => 'Berhasil menambahkan data Keluarga', 'data' => $keluarga], 201);
    //     } catch (Exception $e) {
    //         Log::error('Error storing keluarga data: ' . $e->getMessage());
    //         return response()->json(['Message' => 'Terjadi kesalahan saat menambahkan data Keluarga.', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function update(Request $request, $no_kk)
    {
        try {
            $request->validate([
                'alamat' => 'nullable|string|max:255',
                'lokasi_lengkap' => 'nullable|string|max:255',
                'nama_ibu' => 'nullable|string|max:255',
            ]);

            $validatedData = $request->only(['alamat', 'lokasi_lengkap', 'nama_ibu']);
            if (isset($validatedData['alamat'])) {
                $validatedData['alamat'] = htmlspecialchars($validatedData['alamat'], ENT_QUOTES, 'UTF-8'); // Sanitasi alamat
            }
            if (isset($validatedData['lokasi_lengkap'])) {
                $validatedData['lokasi_lengkap'] = htmlspecialchars($validatedData['lokasi_lengkap'], ENT_QUOTES, 'UTF-8'); // Sanitasi lokasi_lengkap
            }
            if (isset($validatedData['nama_ibu'])) {
                $validatedData['nama_ibu'] = htmlspecialchars($validatedData['nama_ibu'], ENT_QUOTES, 'UTF-8'); // Sanitasi nama_ibu
            }

            $keluarga = Keluarga::findOrFail($no_kk);
            $keluarga->update($validatedData);

            return response()->json(['Message' => 'Berhasil update data Keluarga', 'data' => $keluarga], 200);
        } catch (Exception $e) {
            Log::error('Error updating keluarga data: ' . $e->getMessage());
            return response()->json(['Message' => 'Terjadi kesalahan saat memperbarui data Keluarga.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($no_kk)
    {
        try {
            $keluarga = Keluarga::findOrFail($no_kk);
            $keluarga->delete();

            return response()->json(['Message' => 'Data Keluarga berhasil dihapus', 'data' => $keluarga], 200);
        } catch (Exception $e) {
            Log::error('Error deleting keluarga data: ' . $e->getMessage());
            return response()->json(['Message' => 'Terjadi kesalahan saat menghapus data Keluarga.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($no_kk)
    {
        try {
            $keluarga = Keluarga::where('no_kk', $no_kk)->firstOrFail();
            return response()->json(['Message' => 'Data Keluarga berhasil didapatkan', 'data' => $keluarga], 200);
        } catch (Exception $e) {
            Log::error('Error fetching keluarga data: ' . $e->getMessage());
            return response()->json(['Message' => 'Data Keluarga tidak ditemukan', 'error' => $e->getMessage()], 404);
        }
    }

    public function index()
    {
        try {
            $keluarga = Keluarga::all();
            return response()->json(['Message' => 'Semua data Keluarga berhasil didapatkan', 'data' => $keluarga], 200);
        } catch (Exception $e) {
            Log::error('Error fetching all keluarga data: ' . $e->getMessage());
            return response()->json(['Message' => 'Terjadi kesalahan saat mengambil data Keluarga.', 'error' => $e->getMessage()], 500);
        }
    }
}
