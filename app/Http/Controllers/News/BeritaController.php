<?php

namespace App\Http\Controllers\News;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BeritaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $berita = Berita::all();
        return response()->json([
            'message' => 'Semua data berita berhasil didapatkan',
            'data' => $berita,
        ], 200);
    }

    public function show($id)
    {
        try {
            $berita = Berita::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Data berita tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Data berita berhasil didapatkan',
            'data' => $berita,
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string|max:255',
                'kategori' => 'required|string|max:100',
                'tempat' => 'required|string|max:100',
                'waktu' => 'required|string|regex:/\d{2}:\d{2} - \d{2}:\d{2} WIB/',
                'tanggal' => 'required|date',
            ]);

            $user_id = Auth::id();

            $posyandu_id = DB::table('posyandu_user')
                ->where('user_id', $user_id)
                ->value('posyandu_id');

            if (!$posyandu_id) {
                return response()->json(['message' => 'User belum terhubung dengan posyandu'], 400);
            }

            $validated['posyandu_id'] = $posyandu_id;

            $berita = Berita::create($validated);

            return response()->json([
                'message' => 'Berhasil menambahkan data berita',
                'data' => $berita
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data berita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string|max:255',
                'kategori' => 'required|string|max:100',
                'tempat' => 'required|string|max:100',
                'waktu' => 'required|string|regex:/\d{2}:\d{2} - \d{2}:\d{2} WIB/',
                'tanggal' => 'required|date',
            ]);

            $berita = Berita::find($id);

            if (!$berita) {
                return response()->json(['message' => 'Data berita tidak ditemukan'], 404);
            }

            $berita->update($validated);

            return response()->json([
                'message' => 'Berhasil update data berita',
                'data' => $berita
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengupdate data berita',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $berita = Berita::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Data berita tidak ditemukan'], 404);
        }

        $berita->delete();

        return response()->json(['message' => 'Data berita berhasil dihapus', 'data' => $berita], 200);
    }
}
