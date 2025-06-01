<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

public function showProfile()
{
    try {
        $user = User::select('nama_lengkap', 'nik', 'kontak', 'email', 'password', 'foto_profil')
                    ->findOrFail(Auth::id());

        if ($user->foto_profil) {
            $user->foto_profil = asset('storage/app/public/' . $user->foto_profil);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data profil pengguna berhasil diambil.',
            'data' => $user
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat mengambil data profil: ' . $e->getMessage()
        ], 500);
    }
}


    public function updateProfile(Request $request)
    {
        try {
            $user = User::findOrFail(Auth::id());

            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|min:8',
                'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'kontak' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($request->only('nama_lengkap', 'email', 'kontak'));

            if ($request->filled('password') && !Hash::check($request->password, $user->password)) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            if ($request->hasFile('foto_profil')) {
                if ($user->foto_profil && Storage::exists($user->foto_profil)) {
                    Storage::delete($user->foto_profil);
                }

                $path = $request->file('foto_profil')->store('Photo-Profile');
                if (!$path) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengupload foto profil.'
                    ], 500);
                }

                $user->update(['foto_profil' => $path]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui.',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = User::findOrFail(Auth::id());

            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password saat ini salah'
                ], 400);
            }
            if (!Hash::check($request->password, $user->password)) {
                $user->update(['password' => Hash::make($request->password)]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui password: ' . $e->getMessage()
            ], 500);
        }
    }
}
