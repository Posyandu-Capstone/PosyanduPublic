<?php

namespace App\Http\Controllers\Authentication;

// use Dotenv\Validator;
use App\Mail\UserRegisteredMail;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'checkNIK', 'updateProfile']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        $user = User::where('email', request('email'))->first();

        if (!$user || $user->status !== 'success') {
            return response()->json(['error' => !$user ? 'Email atau password salah' : 'Tolong registrasi kembali'], !$user ? 404 : 403);
        }
        if ($user->status !== 'success') {
            # code...
        }

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request): JsonResponse
    {
        $messages = [
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.dns' => 'Domain email tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
            // 'nik.unique' => 'NIK sudah digunakan.',
        ];

        $validator = Validator::make($request->all(), [
            // 'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email|max:255',
            'password' => 'required|min:8',
            'nik' => 'required|string',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'pending',
                'nik' => $request->nik
            ]);

            // Kirim email plain text tanpa view
            // Mail::raw("Pendaftaran baru:\n\nNama: {$user->nama_lengkap}\nEmail: {$user->email}\nPosisi: {$user->posisi}", function ($message) {
            //     $message->to('ahmadakrom563@gmail.com')
            //             ->subject('Notifikasi Pendaftaran Baru');
            // });

            DB::commit();

            return response()->json([
                'message' => 'Pendaftaran berhasil',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Pendaftaran gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Get the authenticated User. 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(Auth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function checkNIK(Request $request)
    {
        $request->validate([
            'nik' => 'required|string'
        ]);

        $exist = User::where('nik', $request->nik)->exists();

        if ($exist) {
            return response()->json(['message' => 'NIK sudah terdaftar'], 409);
        }

        return response()->json(['message' => 'NIK tersedia'], 200);
    }

    public function showLengkapiData()
    {
        return view('auth.lengkapi-data');
    }

    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'nik' => 'required|string',
                'email'=> 'required|string',
                'nama_lengkap' => 'required|string',
                'posyandu_id' => 'nullable|exists:posyandu,id',
                'posisi' => 'required|string',
                'kontak' => 'required|string|max:255',
                'foto_ktp' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
            ]);

            DB::beginTransaction();

            $user = User::where('email', $request->email)->firstOrFail();

            $pathFK = $request->file('foto_ktp')->store('KTP', 'public');
            $pathFP = $request->file('foto_profil')->store('Photo-Profile', 'public');

            $user->update([
                'nama_lengkap' => $request->nama_lengkap,
                'nik'=> $request->nik,
                'posisi' => $request->posisi,
                'kontak' => $request->kontak,
                'foto_ktp' => $pathFK,
                'foto_profil' => $pathFP
            ]);

            if ($request->posisi === 'Kader') {
                DB::table('posyandu_user')->insert([
                    'user_id' => $user->id,
                    'posyandu_id' => $request->posyandu_id
                ]);
            }


            DB::commit();

            // Mail::to('ahmadakrom777@gmail.com')->send(new UserRegisteredMail($user));

            return response()->json([
                'message' => 'Data berhasil dilengkapi',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal update profil: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getUserByEmail(Request $request)
    {
        $email = $request->query('email');

        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json($user);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve user data',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateNik(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nik' => 'required|string|max:16',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $user->nik = $request->nik;
        $user->save();

        return response()->json(['message' => 'NIK berhasil diperbarui']);
    }
}
