<?php

namespace App\Http\Controllers\Pemeriksaan;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Pemeriksaan;
use App\Models\{Warga, PemeriksaanBalita, AnggotaKeluarga, Keluarga, Posyandu, Antrian};
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PemeriksaanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function search(Request $request)
    {
        try {
            $keyword = trim($request->input('nama'));
            $posko = trim($request->input('posko'));
            $age = trim($request->input('age'));
            $today = now();

            $user = Auth::user();

            if ($user->posisi === 'Admin Desa') {
                $allowedPosko = Posyandu::pluck('id')->toArray();
            } elseif ($user->posisi === 'Kader') {
                $allowedPosko = $user->posyandus->pluck('id')->toArray();
            } else {
                abort(403, 'Unauthorized role');
            }

            $query = AnggotaKeluarga::query()
                ->whereHas('pemeriksaan')
                ->whereHas('keluarga', function ($data) use ($allowedPosko, $posko) {
                    $poskoId = Posyandu::where('nama_posyandu', $posko)->first();
                    if (!empty($posko) && $poskoId && in_array($poskoId->id, $allowedPosko)) {
                        Log::info('Posko valid dan dipilih:', ['posko_id' => $poskoId->id, 'allowedPosko' => $allowedPosko]);
                        $data->where('posyandu_id', $poskoId->id);
                    } else if (!empty($posko)) {
                        Log::info('Posko dipilih tidak valid atau tidak di allowedPosko:', ['posko' => $posko, 'allowedPosko' => $allowedPosko]);
                        $data->whereRaw('1 = 0');
                    } else {
                        Log::info('Posko kosong, pakai allowedPosko:', ['allowedPosko' => $allowedPosko]);
                        $data->whereIn('posyandu_id', $allowedPosko);
                    }
                });

            if (!empty($keyword)) {
                $query->where('nama_anggota_keluarga', 'LIKE', '%' . $keyword . '%');
            }

            if (!empty($posko)) {
                $query->where('posko_terdaftar', $posko);
            }

            if (!empty($age)) {
                switch (strtolower($age)) {
                    case 'balita':
                        $query->whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) < 5", [$today]);
                        break;
                    case 'remaja':
                        $query->whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 5 AND 17", [$today]);
                        break;
                    case 'dewasa':
                        $query->whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) BETWEEN 18 AND 40", [$today]);
                        break;
                    case 'lansia':
                        $query->whereRaw("TIMESTAMPDIFF(YEAR, tanggal_lahir, ?) > 40", [$today]);
                        break;
                    default:
                        return response()->json(['message' => 'Kategori usia tidak valid'], 400);
                }
            }

            $anggotaList = $query->with('keluarga')->get();

            if ($anggotaList->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data tidak ditemukan.',
                    'data' => []
                ]);
            }

            $data = $anggotaList->map(function ($anggota) {
                return [
                    'nik' => $anggota->nik,
                    'nama' => $anggota->nama_anggota_keluarga,
                    'alamat' => $anggota->keluarga->alamat ?? null,
                    'nama_ibu' => $anggota->keluarga->nama_ibu ?? null,
                    'posko' => $anggota->posko_terdaftar,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => $data->isEmpty() ? 'Data tidak ditemukan.' : 'Data berhasil dicari.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat melakukan pencarian: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getAllowedPoskoIds()
    {
        $user = Auth::user();

        if ($user->posisi === 'Admin Desa') {
            return Posyandu::select('posyandu.id')->pluck('id')->toArray();
        } elseif ($user->posisi === 'Kader') {
            return $user->posyandus->pluck('id')->toArray();
        } else {
            abort(403, 'Unauthorized role');
        }
    }

    public function index()
    {
        $allowedPosko = $this->getAllowedPoskoIds();

        $data = Pemeriksaan::with('anggota_keluarga')
            ->whereHas('anggota_keluarga.keluarga', function ($query) use ($allowedPosko) {
                $query->whereIn('posyandu_id', $allowedPosko);
            })
            ->get()
            ->map(
                function ($pmk) {
                    // Log::info('Pemeriksaan Data: ', $pmk->toArray());
        
                    $bayi = $pmk->anggota_keluarga;
                    $keluargaId = $bayi?->keluarga_no_kk;

                    $ibu = AnggotaKeluarga::where('keluarga_no_kk', $keluargaId)
                        ->where('posisi_keluarga', 'Istri')
                        ->first();

                    $alamat = Keluarga::where('no_kk', $keluargaId)->value('alamat');

                    return [
                        'nama' => $bayi?->nama_anggota_keluarga,
                        'nik' => $bayi?->nik,
                        'alamat' => $alamat,
                        'nama_ibu' => $ibu?->nama_anggota_keluarga,
                    ];
                }
            );

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diambil',
            'data' => $data
        ]);
    }

    public function show($nik)
    {
        $pemeriksaan = Pemeriksaan::with('anggota_keluarga')
            ->where('anggota_keluarga_nik', $nik)
            ->first();

        $bayi = $pemeriksaan->anggota_keluarga->keluarga_no_kk;

        // dd($bayi);

        $ibu = AnggotaKeluarga::where('keluarga_no_kk', $bayi)
            ->where('posisi_keluarga', 'Istri')
            ->first();

        $alamat = Keluarga::where('no_kk', $bayi)->value('alamat');

        $user_id = Warga::where('keluarga_no_kk', $bayi)->value('anggota_keluarga_nik');

        if (!$pemeriksaan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pemeriksaan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data pemeriksaan berhasil diambil.',
            'data' => [
                'pemeriksaan' => $pemeriksaan,
                'ibu' => $ibu,
                'alamat' => $alamat,
                'user_id' => $user_id
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $wargaAuth = Auth::guard('warga')->user();
            $userAuth = Auth::user();

            if ($wargaAuth) {
                logger('Login sebagai: WARGA');
            } elseif ($userAuth) {
                logger('Login sebagai: USER (Bidan/Admin)');
            } else {
                logger('Tidak ada user yang login');
            }

            if ($wargaAuth) {
                $validated = $request->validate([
                    'berita_id' => 'required|numeric|exists:berita,id',
                    'anggota_keluarga_nik' => 'required|string|exists:anggota_keluarga,nik',
                    // 'tipe_pemeriksaan' => 'nullable|string|in:Balita,Ibu Hamil,Lansia',
                    // 'catatan' => 'nullable|string',
                    // 'diagnosa' => 'nullable|string',
                    // 'tindakan' => 'nullable|string',
                ]);

                $anggota = AnggotaKeluarga::where('nik', $validated['anggota_keluarga_nik'])->firstOrFail();
                $warga = Warga::where('keluarga_no_kk', $anggota->keluarga_no_kk)->firstOrFail();

                $jumlahAntrian = Antrian::where('berita_id', $request->berita_id)->count();
                $nomorAntrian = $jumlahAntrian + 1;

                $antrian = Antrian::create([
                    'berita_id' => $request->berita_id,
                    'nomor_antrian' => $nomorAntrian
                ]);

                $pemeriksaan = Pemeriksaan::create([
                    'user_id' => $warga->id,
                    'anggota_keluarga_nik' => $anggota->nik,
                    'antrian_id' => $antrian->id,
                    'tipe_pemeriksaan' => "balita"
                ]);

                if ($request->tipe_pemeriksaan === 'Balita') {
                    PemeriksaanBalita::create([
                        'pemeriksaan_id' => $pemeriksaan->id
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Pemeriksaan berhasil dibuat.',
                    'data' => $pemeriksaan
                ], 201);
            } elseif ($userAuth) {
                $validated = $request->validate([
                    'user_id' => 'required|string|exists:warga,anggota_keluarga_nik',
                    'berita_id' => 'required|numeric|exists:berita,id',
                    'anggota_keluarga_nik' => 'required|string|exists:anggota_keluarga,nik',
                ]);

                $anggota = AnggotaKeluarga::where('nik', $validated['anggota_keluarga_nik'])->firstOrFail();
                $warga = Warga::where('anggota_keluarga_nik', $validated['user_id'])->firstOrFail();

                $jumlahAntrian = Antrian::where('berita_id', $request->berita_id)->count();
                $nomorAntrian = $jumlahAntrian + 1;

                $antrian = Antrian::create([
                    'berita_id' => $request->berita_id,
                    'nomor_antrian' => $nomorAntrian
                ]);

                // logger('Data anggota:', $anggota->toArray());

                if ($anggota->posisi_keluarga == "Anak") {
                    try {
                        $pemeriksaan = Pemeriksaan::create([
                            'user_id' => $warga->id,
                            'anggota_keluarga_nik' => $anggota->nik,
                            'antrian_id' => $antrian->id,
                            'tipe_pemeriksaan' => "balita"
                        ]);
                        logger('Pemeriksaan berhasil dibuat:', [$pemeriksaan]);

                        PemeriksaanBalita::create([
                            'pemeriksaan_id' => $pemeriksaan->id
                        ]);

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Pemeriksaan berhasil dibuat.',
                            'id' => $pemeriksaan->anggota_keluarga_nik,
                            'data' => $pemeriksaan
                        ], 201);
                    } catch (\Exception $e) {
                        logger('Gagal membuat pemeriksaan:', [$e->getMessage()]);
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Gagal membuat pemeriksaan.',
                        ], 500);
                    }
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'Pemeriksaan hanya dapat dilakukan untuk anggota keluarga dengan posisi "Anak".',
                ], 422);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        }
    }
    public function getByBeritaId($beritaId)
    {
        $data = DB::table('pemeriksaan')
            ->join('antrian', 'pemeriksaan.antrian_id', '=', 'antrian.id')
            ->join('anggota_keluarga', 'pemeriksaan.anggota_keluarga_nik', '=', 'anggota_keluarga.nik')
            ->where('antrian.berita_id', $beritaId)
            ->select(
                'pemeriksaan.anggota_keluarga_nik as nik',
                'anggota_keluarga.nama_anggota_keluarga as nama'
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }


    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'imunisasi' => 'required|string',
                'tanggal_periksa' => 'required|date',
                'berat_badan' => 'required|numeric',
                'tinggi_badan' => 'required|numeric',
                'PMT' => 'required|string',
                'total_PMT' => 'required|numeric',
                'ASI' => 'required|boolean',
                'vit' => 'required|string',
                'umur' => 'required|numeric',
                'Suplemen' => 'required|string',
            ]);

            $pemeriksaan = Pemeriksaan::with('anggota_keluarga')->find($id);

            // Hitung umur dalam bulan dari tanggal lahir anggota keluarga
            if ($pemeriksaan && $pemeriksaan->anggota_keluarga) {
                $tanggalLahir = $pemeriksaan->anggota_keluarga->tanggal_lahir;
                $umurBulan = Carbon::parse($tanggalLahir)->diffInMonths(Carbon::now());
            } else {
                $umurBulan = null;
            }

            // Hitung rasio jika umur valid dan lebih dari 0
            if ($umurBulan !== null && $umurBulan > 0) {
                $bbPerUmur = round($validated['berat_badan'] / $umurBulan, 2);
                $tbPerUmur = round($validated['tinggi_badan'] / $umurBulan, 2);
            } elseif ($umurBulan === 0) {
                $bbPerUmur = 'Umur 0 bulan';
                $tbPerUmur = 'Umur 0 bulan';
            } else {
                $bbPerUmur = null;
                $tbPerUmur = null;
            }

            // BB per TB tetap bisa dihitung karena tidak bergantung umur
            $bbPerTb = round($validated['berat_badan'] / $validated['tinggi_badan'], 2);

            Log::info('BB per Umur: ' . $bbPerUmur);
            Log::info('TB per Umur: ' . $tbPerUmur);
            Log::info('BB per TB: ' . $bbPerTb);

            // Update pemeriksaan utama
            $pemeriksaan->update([
                'tipe_pemeriksaan' => 'Balita',
            ]);

            // Update data balita
            $pemeriksaanBalita = PemeriksaanBalita::findOrFail($id);
            $pemeriksaanBalita->update([
                'tanggal_timbang' => $validated['tanggal_periksa'],
                'berat_badan_kg' => $validated['berat_badan'],
                'tinggi_badan' => $validated['tinggi_badan'],
                'bb_u' => $bbPerUmur,
                'tb_u' => $tbPerUmur,
                'bb_tb' => $bbPerTb,
                'imunisasi' => $validated['imunisasi'],
                'umur' => $validated['umur'],
                'PMT' => $validated['PMT'],
                'total_PMT' => $validated['total_PMT'],
                'ASI' => $validated['ASI'],
                'vit' => $validated['vit'],
                'Suplemen' => $validated['Suplemen']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data pemeriksaan berhasil diperbarui.',
                'data' => $pemeriksaanBalita
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        }
    }


    public function destroy($id)
    {
        $pemeriksaan = Pemeriksaan::findOrFail($id);
        $pemeriksaan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data pemeriksaan berhasil dihapus.',
            'data' => $pemeriksaan
        ], 200);
    }

    public function riwayatPemeriksaanBalita($nik)
    {
        $riwayat = PemeriksaanBalita::with('pemeriksaan')
            ->whereHas('pemeriksaan', function ($query) use ($nik) {
                $query->where('anggota_keluarga_nik', $nik);
            })
            ->orderBy('tanggal_timbang', 'desc')
            ->get();

        if ($riwayat->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Riwayat pemeriksaan balita tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Riwayat Pemeriksaan Balita Berhasil Diambil',
            'data' => $riwayat
        ], 200);
    }

    public function riwayatPemeriksaan($id)
    {
        $riwayat = PemeriksaanBalita::with('pemeriksaan')
            ->where('pemeriksaan_id', $id)
            ->first();

        if (!$riwayat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Riwayat pemeriksaan balita tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Riwayat Pemeriksaan Balita Berhasil Diambil',
            'data' => $riwayat
        ], 200);
    }


    // -------------------> Logika <--------------------
    private function hitungBBPerUmur($berat, $umur)
    {
        $hasil = $berat / $umur;
        if ($hasil < 2)
            return 'Kurang';
        if ($hasil <= 5)
            return 'Normal';
        return 'Lebih';
    }

    private function hitungTBPerUmur($tinggi, $umur)
    {
        $hasil = $tinggi / $umur;
        if ($hasil < 85)
            return 'TB Pendek';
        if ($hasil <= 110)
            return 'TB Normal';
        return 'TB Tinggi';
    }

    private function hitungBBPerTB($berat, $tinggi)
    {
        $hasil = $berat / ($tinggi / 100);
        if ($hasil < 13)
            return 'Gizi Buruk';
        if ($hasil < 15)
            return 'Gizi Kurang';
        if ($hasil < 18)
            return 'Gizi Baik';
        return 'Gizi Lebih';
    }

}
