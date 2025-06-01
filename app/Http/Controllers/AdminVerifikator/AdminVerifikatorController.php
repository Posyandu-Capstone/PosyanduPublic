<?php

namespace App\Http\Controllers\AdminVerifikator;

use App\Http\Controllers\Controller;
use App\Mail\StatusDiterimaMail;
use App\Mail\StatusDitolakMail;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class AdminVerifikatorController extends Controller
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
        $users = User::select('id', 'nama_lengkap', 'nik', 'created_at', 'status')
            ->orderBy('created_at', 'desc')
            ->get();

        $result = $users->map(function ($user, $index) {
            return [
                'nomor' => $index + 1,
                'nama_lengkap' => $user->nama_lengkap,
                'nik' => $user->nik,
                'waktu_pengajuan' => Carbon::parse($user->created_at)->translatedFormat('d F Y'),
                'status' => $user->status,
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
public function show($nik)
{
    $user = User::where('nik', $nik)->first();

    if (!$user) {
        return response()->json([
            'message' => 'Pengguna tidak ditemukan',
        ], 404);
    }

    return response()->json([
        'message' => 'Detail pengguna ditemukan',
        'data' => [
            'id' => $user->id,
            'email' => $user->email,
            'posisi' => $user->posisi,
            'kontak' => $user->kontak,
            'nama_lengkap' => $user->nama_lengkap,
            'nik' => $user->nik,
            'waktu_pengajuan' => Carbon::parse($user->created_at)->translatedFormat('d F Y'),
            'status' => $user->status,
            'foto_ktp' => $user->foto_ktp ? asset('storage/app/public/' . $user->foto_ktp) : null,
        ]
    ]);
}


    public function update(Request $request, $nik)
    {
        $request->validate([
            'status' => 'required|in:pending,success,denied'
        ]);

        $user = User::where('nik', $nik)->firstOrFail();
        $user->status = $request->status;
        $user->save();

        if ($user->status === 'success') {
            Mail::to($user->email)->send(new StatusDiterimaMail($user));
        } elseif ($user->status === 'denied') {
            Mail::to($user->email)->send(new StatusDitolakMail($user));
        }

        return response()->json([
            'message' => 'Status pengguna berhasil diperbarui',
            'data' => [
                'nik' => $user->nik,
                'status' => $user->status
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