<?php

namespace App\Http\Controllers\Posyandu;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Posyandu;
use Illuminate\Http\Request;

class DataPosyanduController extends Controller
{
    
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

    public function getPosyandu()
    {
        $posyandu = Posyandu::with(['desa', 'users'])
            ->get()
            ->map(function ($item) {
                $ketua = $item->users->firstWhere('posisi', 'Kader');

                return [
                    'id' => $item->id,
                    'nama_posyandu' => $item->nama_posyandu,
                    'nama_desa' => optional($item->desa)->Nama_Desa,
                    'nama_koordinator' => optional($ketua)->nama_lengkap ?? 'Belum ditentukan',
                    'jumlah_anggota' => $item->users->count(),
                ];
            });

        return response()->json([
            'message' => 'Daftar posyandu berhasil diambil',
            'data' => $posyandu
        ], 200);
    }
    
    // public function showPosyandu($id)
    // {
    //     $posyandu = Posyandu::findOrFail($id);
    //     return response()->json([
    //         'Message' => 'Data posyandu berhasil didapatkan',
    //         'data' => $posyandu,
    //     ], 200);
    // }

    // public function showPosyanduAll()
    // {
    //     $posyandu = Posyandu::all();
    //     return response()->json([
    //         'Message' => 'Semua data posyandu berhasil didapatkan',
    //         'data' => $posyandu,
    //     ], 200);
    // }

    public function addPosyandu(Request $request)
    {
        
        $request->validate([
            'nama_posyandu' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nama_desa' => 'required|string',
            'kecamatan_id' => 'required|string',
        ]);

        $desa = Desa::where('nama_desa', $request->nama_desa)->first();

        if (!$desa) {
            return response()->json(['message' => 'Desa tidak ditemukan'], 404);
        }

        $kecamatan = Posyandu::create([
            'nama_posyandu' => $request->nama_posyandu,
            'alamat' => $request->alamat,
            'desa_id' => $desa->code,
            'kecamatan_id' => $request->kecamatan_id,
        ]);
        return response()->json(['Message' => 'Berhasil menambahkan data posyandu', 'data' => $kecamatan], 201);
    }

    public function updatePosyandu(Request $request, $id)
    {
        $request->validate([
            'nama_posyandu' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'nama_desa' => 'required|string',
        ]);

        $desa = Desa::where('nama_desa', $request->nama_desa)->first();

        if (!$desa) {
            return response()->json(['message' => 'Desa tidak ditemukan'], 404);
        }
    
        $updated = Posyandu::where('id', $id)->update([
            'nama_posyandu' => $request->nama_posyandu,
            'alamat' => $request->alamat,
            'desa_id' => $desa->code
        ]);
        return response()->json(['Message' => $updated ? 'Berhasil update data posyandu' : 'Gagal update data posyandu'], $updated ? 200 : 400);
    }

    public function destroyPosyandu($id)
    {
        $posyandu = Posyandu::findOrFail($id);
        $posyandu->delete();
        return response()->json(['Message' => 'Data posyandu berhasil dihapus', 'data' => $posyandu], 200);
    }

    public function showPosyandu($id)
    {
        $posyandu = Posyandu::findOrFail($id);
        return response()->json([
            'Message' => 'Data posyandu berhasil didapatkan',
            'data' => $posyandu,
        ], 200);
    }
}
