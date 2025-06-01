<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluarga;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function showLaporan()
    {
        $user = Auth::user();

        $posyanduIds = match ($user->posisi) {
            'Admin Desa' => Posyandu::pluck('id')->toArray(),
            'Kader' => $user->posyandus->pluck('id')->toArray(),
            default => abort(403, 'Unauthorized role'),
        };
        $posyandus = Posyandu::with(['keluargas.anggotaKeluarga'])->whereIn('id', $posyanduIds)->get();

        $laporan = $posyandus->map(function ($pos) {
            $totalAnggota = $pos->keluargas->sum(fn($keluarga) => $keluarga->anggotaKeluarga->count());

            $totalDatang = $pos->keluargas->sum(function ($keluarga) {
                return $keluarga->anggotaKeluarga->filter(
                    fn($anggota) => $anggota->pemeriksaan()->exists()
                )->count();
            });

            return [
                'id' => $pos->id,
                'nama_posyandu' => $pos->nama_posyandu,
                'sasaran' => $totalAnggota,
                'datang' => $totalDatang
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $laporan
        ]);
    }
}
