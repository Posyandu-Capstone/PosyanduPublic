<?php

namespace App\Http\Controllers\AdminVerifikator;

use App\Http\Controllers\Controller;
use App\Mail\StatusDiterimaMail;
use App\Mail\StatusDitolakMail;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class WargaVerifikatorController extends Controller
{
    public function __construct()
    {
        Carbon::setLocale('id'); // Set locale untuk Carbon
    }

    /**
     * Menampilkan daftar pengguna dengan status "pending" untuk diverifikasi.
     */
    public function index()
    {
        $wargas = Warga::select('id', 'nama_lengkap', 'anggota_keluarga_nik', 'created_at', 'status')
            ->orderBy('created_at', 'desc')
            ->get();

        $result = $wargas->map(function ($wargas, $index) {
            return [
                'nomor' => $index + 1,
                'nama_lengkap' => $wargas->nama_lengkap,
                'anggota_keluarga_nik' => $wargas->anggota_keluarga_nik,
                'waktu_pengajuan' => Carbon::parse($wargas->created_at)->translatedFormat('d F Y'),
                'status' => $wargas->status,
            ];
        });

        return response()->json([
            'message' => 'Daftar pengguna berhasil diambil',
            'data' => $result
        ]);
    }

    /**
     * Menampilkan detail pengguna tertentu.
     */
public function show($anggota_keluarga_nik)
{
    $wargas = Warga::where('anggota_keluarga_nik', $anggota_keluarga_nik)->first();

    if (!$wargas) {
        return response()->json([
            'message' => 'Pengguna tidak ditemukan',
        ], 404);
    }

    return response()->json([
        'message' => 'Detail pengguna ditemukan',
        'data' => [
            'id' => $wargas->id,
            'email' => $wargas->email,
            'no_telp' => $wargas->no_telp,
            'nama_lengkap' => $wargas->nama_lengkap,
            'anggota_keluarga_nik' => $wargas->anggota_keluarga_nik,
            'waktu_pengajuan' => Carbon::parse($wargas->created_at)->translatedFormat('d F Y'),
            'status' => $wargas->status,
            // 'foto_ktp' => $wargas->foto_ktp ? asset('storage/app/public/' . $wargas->foto_ktp) : null,
        ]
    ]);
}


    public function update(Request $request, $anggota_keluarga_nik)
    {
        $request->validate([
            'status' => 'required|in:pending,success,denied'
        ]);

        $wargas = Warga::where('anggota_keluarga_nik', $anggota_keluarga_nik)->firstOrFail();
        $wargas->status = $request->status;
        $wargas->save();

        if ($wargas->status === 'success') {
            Mail::to($wargas->email)->send(new StatusDiterimaMail($wargas));
        } elseif ($wargas->status === 'denied') {
            Mail::to($wargas->email)->send(new StatusDitolakMail($wargas));
        }

        return response()->json([
            'message' => 'Status pengguna berhasil diperbarui',
            'data' => [
                'anggota_keluarga_nik' => $wargas->anggota_keluarga_nik,
                'status' => $wargas->status
            ]
        ]);
    }


    public function destroy($id)
    {
        return response()->json([
            'message' => 'Fitur hapus tidak diaktifkan untuk admin verifikator'
        ], 403);
    }
}