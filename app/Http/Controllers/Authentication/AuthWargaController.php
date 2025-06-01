<?php

namespace App\Http\Controllers\Authentication;

use App\Models\{Warga, AnggotaKeluarga, Keluarga, Posyandu};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{Auth, Hash, Validator};
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\MobilePasswordEmail;
use App\Mail\SendDefaultPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AuthWargaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:warga', ['except' => ['login', 'register', 'anggotaKeluarga', 'forgetPassword', 'resetNow', 'getPosyanduList']]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if ($token = Auth::guard('warga')->attempt($credentials)) {
            // Setelah berhasil attempt, user sudah authenticated di guard ini
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Email atau password salah!'], 401);
        // return Auth::guard('warga')->attempt($credentials)
        //     ? $this->respondWithToken(Auth::guard('warga')->attempt($credentials))
        //     : response()->json(['error' => 'Email atau password salah!'], 401);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:warga,email',
            'password' => 'required|min:6',
            'posyandu' => 'required|int',
            'nik' => 'required|string|unique:warga,anggota_keluarga_nik',
            'no_kk' => 'required|string|unique:keluarga,no_kk',
            'no_telp' => 'nullable|string|max:14',
            'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'nullable|date',
        ], [
            'nik.unique' => 'NIK sudah terpakai.',
            'no_kk.unique' => 'Nomor KK sudah terpakai.'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('nik')) {
                return response()->json(['message' => 'NIK sudah terpakai.'], 422);
            }

            if ($errors->has('no_kk')) {
                return response()->json(['message' => 'Nomor KK sudah terpakai.'], 422);
            }

            return response()->json(['errors' => $errors], 422);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $keluarga = Keluarga::firstOrCreate(['no_kk' => $request->no_kk, 'posyandu_id' => $request->posyandu]);
            $anggota = AnggotaKeluarga::where('nik', $request->nik)->first();

            if (!$anggota) {
                $anggota = AnggotaKeluarga::create([
                    'nik' => $request->nik,
                    'keluarga_no_kk' => $request->no_kk,
                    'nama_anggota_keluarga' => $request->nama_lengkap,
                    'posisi_keluarga' => 'Kepala keluarga',
                    'posko_terdaftar' => '-',
                ]);
            }

            Log::info('Data anggota keluarga:', $anggota->toArray());
            Log::info('Request nik:', ['nik' => $request->nik]);

            $warga = Warga::create([
                'anggota_keluarga_nik' => $anggota->nik,
                'keluarga_no_kk' => $keluarga->no_kk,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'jenis_kelamin' => $request->input('jenis_kelamin'),
                'Kepala_Keluarga' => $request->boolean('Kepala_Keluarga', false),
                'tanggal_lahir' => $request->tanggal_lahir,
                'no_telp' => $request->no_telp,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pendaftaran berhasil!',
                'warga' => $warga,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Pendaftaran gagal!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function me(): JsonResponse
    {
        return response()->json(Auth::guard('warga')->user());
    }

    public function logout(): JsonResponse
    {
        Auth::guard('warga')->logout();
        return response()->json(['message' => 'Logout berhasil!']);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::refresh());
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => Auth::guard('warga')->user(),
        ]);
    }

    public function anggotaKeluarga(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_anggota_keluarga' => 'required|string|max:255',
            'nik' => 'required|string|unique:anggota_keluarga,nik',
            'keluarga_no_kk' => 'required|string|max:16',
            'posisi_keluarga' => 'required|string|in:Istri,Anak',
            'anak_ke' => 'nullable|numeric',
            'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $anggota = AnggotaKeluarga::create([
                'nama_anggota_keluarga' => $request->nama_anggota_keluarga,
                'nik' => $request->nik,
                'keluarga_no_kk' => $request->keluarga_no_kk,
                'posisi_keluarga' => $request->posisi_keluarga,
                'anak_ke' => $request->anak_ke,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Anggota keluarga berhasil ditambahkan',
                'data' => $anggota
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Warga::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'nullable|string|max:255',
                'no_telp' => 'nullable|string|max:255|unique:warga,no_telp,' . $user->id,
                'nik' => 'nullable|string|max:16|unique:warga,anggota_keluarga_nik,' . $user->id,
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|string|in:Laki-laki,Perempuan'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $anggota = AnggotaKeluarga::where('nik', $user->anggota_keluarga_nik)->first();
            $anggota->update($request->only('nama_anggota_keluarga', 'no_telp', 'anggota_keluarga_nik', 'tanggal_lahir', 'jenis_kelamin'));

            $user->update($request->only('nama_lengkap', 'no_telp', 'anggota_keluarga_nik', 'tanggal_lahir', 'jenis_kelamin'));


            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui.',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProfile($id)
    {
        $user = Warga::where('id', $id)->first();

        return response()->json([
            'nama_lengkap' => $user->nama_lengkap,
            'no_telp' => $user->no_telp,
            'nik' => $user->anggota_keluarga_nik,
            'tanggal_lahir' => $user->tanggal_lahir,
            'jenis_kelamin' => $user->jenis_kelamin
        ]);
    }

    public function updateEmail($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Warga::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'email_baru' => 'required|email|unique:warga,email,' . $user->id,
                'email_sekarang' => 'required|email',
                'password' => 'required'

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password yang dimasukkan salah.'
                ], 401);
            }

            if ($request->email_sekarang !== $user->email) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email sekarang tidak sesuai dengan data kami.'
                ], 422);
            }

            // $user->update();
            $user->update(['email' => $request->email_baru]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Email berhasil diperbarui.',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Warga::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'password_baru' => 'required|confirmed',
                'password_sekarang' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Hash::check($request->password_sekarang, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password yang dimasukkan salah.'
                ], 401);
            }

            if (Hash::check($request->password_baru, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password baru tidak boleh sama dengan password lama.'
                ], 422);
            }

            $user->update(['password' => Hash::make($request->password_baru)]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui password: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:warga,email',
        ], [
            'email.exists' => 'Email tidak sesuai.',
        ]);

        $warga = Warga::where('email', $request->email)->first();

        if (!$warga) {
            return response()->json([
                'message' => 'Email tidak terdaftar'
            ], 404);
        }

        $data = [
            'nama' => $warga->nama_lengkap,
            'email' => $warga->email
        ];

        Mail::to($warga->email)->send(new MobilePasswordEmail($data));
    }

    public function resetNow(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = Warga::where('email', $request->email)->first();

        $defaultPassword = Str::random(10);
        $user->password = Hash::make($defaultPassword);
        $user->save();

        Mail::to($user->email)->send(new SendDefaultPasswordMail([
            'name' => $user->name,
            'default_password' => $defaultPassword,
        ]));

        return response()->json(['message' => 'Password berhasil direset dan dikirim ke email Anda.']);
    }

    public function getPosyanduList()
    {
        $posyandu = Posyandu::with('desa')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama_posyandu
                ];
            });

        return response()->json([
            'message' => 'Daftar posyandu berhasil diambil',
            'data' => $posyandu
        ], 200);
    }
}
