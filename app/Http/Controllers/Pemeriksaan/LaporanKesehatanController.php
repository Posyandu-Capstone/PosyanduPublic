<?php

namespace App\Http\Controllers\Pemeriksaan;

use App\Models\Keluarga;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{AnggotaKeluarga, Posyandu, Warga};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LaporanKesehatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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

    public function search(Request $request)
    {
        try {
            $keyword = trim($request->input('nama'));
            $posko   = trim($request->input('posko'));
            $age     = trim($request->input('age'));
            $today   = now();

            $allowedPosko = $this->getAllowedPoskoIds();

            $query = AnggotaKeluarga::whereHas('keluarga', function ($data) use ($allowedPosko, $posko) {
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

            $data = $query->with('keluarga')->get()->map(function ($anggota) {
                return [
                    'nik'      => $anggota->nik,
                    'nama'     => $anggota->nama_anggota_keluarga,
                    'alamat'   => $anggota->keluarga->alamat ?? null,
                    'nama_ibu' => $anggota->keluarga->nama_ibu ?? null,
                    'posko'    => $anggota->keluarga->posyandu_id ?? null, // Gunakan dari relasi keluarga
                ];
            });

            return response()->json([
                'status'  => 'success',
                'message' => $data->isEmpty() ? 'Data tidak ditemukan.' : 'Data berhasil dicari.',
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat melakukan pencarian: ' . $e->getMessage()
            ], 500);
        }
    }

    // 📋 READ semua pasien
    public function index()
    {
        $allowedPosko = $this->getAllowedPoskoIds();

        try {
            $data = AnggotaKeluarga::whereHas('keluarga', callback: function ($query) use ($allowedPosko) {
                $query->whereIn('posyandu_id', $allowedPosko);
            })
                ->with('keluarga')
                ->get()
                ->map(function ($anggota) {
                    return [
                        'nik' => $anggota->nik,
                        'nama' => $anggota->nama_anggota_keluarga,
                        'alamat' => $anggota->keluarga->alamat ?? null,
                        'nama_ibu' => $anggota->keluarga->nama_ibu ?? null
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diambil.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($nik)
    {
        try {
            $anggota = AnggotaKeluarga::with('keluarga')
                ->where('nik', $nik)
                ->firstOrFail();

            $nikAyah = AnggotaKeluarga::where('keluarga_no_kk', $anggota->keluarga_no_kk)
                ->where('posisi_keluarga', 'Kepala Keluarga')
                ->value('nik');

            $nikIbu = AnggotaKeluarga::where('keluarga_no_kk', $anggota->keluarga_no_kk)
                ->where('posisi_keluarga', 'Istri')
                ->value('nik');

            $data = [
                'nama_lengkap' => $anggota->nama_anggota_keluarga,
                'nik' => $anggota->nik,
                'no_kk' => $anggota->keluarga->no_kk,
                'jenis_kelamin' => $anggota->jenis_kelamin,
                'tanggal_lahir' => $anggota->tanggal_lahir ?? null,
                'Anak_Ke' => $anggota->Anak_Ke,
                'nik_ibu' => $nikIbu,
                'nik_ayah' => $nikAyah,
                'nama_ibu' => $anggota->keluarga->nama_ibu ?? null,
                'nama_ayah' => $anggota->keluarga->nama_ayah ?? null,
                'nama_dusun' => $anggota->keluarga->nama_dusun,
                'nomor_telp' => $anggota->nomor_telp,
                'alamat' => $anggota->keluarga->alamat,
                'gakin' => $anggota->gakin ? $anggota->gakin : null,
                'bpjs' => $anggota->bpjs ? $anggota->bpjs : null,
                'penyakit_bawaan' => $anggota->penyakit_bawaan,
                'tinggi_lahir' => $anggota->tinggi_lahir,
                'berat_lahir' => $anggota->berat_lahir,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Data pasien berhasil tampilkan.',
                'data' => $data
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pasien dengan NIK tersebut tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }


    // ➕ CREATE data pasien
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'nama_lengkap' => 'required|string|max:255',
                'nik' => 'required|unique:anggota_keluarga,nik|max:20',
                'no_kk' => 'required|exists:keluarga,no_kk',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'anak_ke' => 'nullable|integer|min:1',
                'tanggal_lahir'   => 'nullable|date',
                'tinggi_lahir' => 'nullable|integer|min:1',
                'penyakit_bawaan' => 'nullable|string|max:255',
                'berat_lahir' => 'nullable|string|max:255',
                'bpjs' => 'nullable|in:Ya,Tidak',
                'gakin' => 'nullable|in:Ya,Tidak',
                'nama_ayah' => 'nullable|string|max:255',
                'nama_ibu' => 'nullable|string|max:255',
                'nik_ayah' => 'nullable|string|max:255',
                'nik_ibu' => 'nullable|string|max:255',
                'nomor_telp' => 'nullable|string|max:20',
                'nama_dusun' => 'nullable|string',
                'alamat' => 'nullable|string|max:15',
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal.',
                    'errors' => $validated->errors()
                ], 422);
            }

            $data = $validated->validated();
            if (!empty($data['tanggal_lahir'])) {
                try {
                    $tgl = str_replace('/', '-', $data['tanggal_lahir']);
                    $data['tanggal_lahir'] = \Carbon\Carbon::createFromFormat('d-m-Y', $tgl)->format('Y-m-d');
                } catch (\Exception $e) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Format tanggal_lahir tidak valid. Gunakan format DD-MM-YYYY atau DD/MM/YYYY.',
                    ], 422);
                }
            }


            $bpjs = $data['bpjs'] === 'Ya' ? true : ($data['bpjs'] === 'Tidak' ? false : null);
            $gakin = $data['gakin'] === 'Ya' ? true : ($data['gakin'] === 'Tidak' ? false : null);

            $anggota = AnggotaKeluarga::create([
                'nama_anggota_keluarga' => trim($data['nama_lengkap']),
                'nik' => $data['nik'],
                'keluarga_no_kk' => $data['no_kk'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                'nomor_telp' => $data['nomor_telp'],
                'Anak_Ke' => $data['anak_ke'] ?? null,
                'tinggi_lahir' => $data['tinggi_lahir'] ?? null,
                'penyakit_bawaan' => trim($data['penyakit_bawaan'] ?? ''),
                'berat_lahir' => trim($data['berat_lahir'] ?? ''),
                'bpjs' => $data['bpjs'] !== null ? $bpjs : null,
                'gakin' => $data['gakin'] !== null ? $gakin : null,
            ]);

            $keluarga = Keluarga::where('no_kk', $data['no_kk'])->first();
            if ($keluarga) {
                $updated = false;

                $updateIfChanged = function ($model, $attribute, $value) use (&$updated) {
                    if (!empty($value) && trim($value) !== trim($model->$attribute ?? '')) {
                        $model->$attribute = $value;
                        $updated = true;
                    }
                };

                if (!empty($data['nik_ayah'])) {
                    $ayah = AnggotaKeluarga::firstOrCreate(
                        ['nik' => $data['nik_ayah']],
                        [
                            'nama_anggota_keluarga' => trim($data['nama_ayah']),
                            'keluarga_no_kk' => $data['no_kk'],
                            'posisi_keluarga' => 'Kepala Keluarga',
                            'jenis_kelamin' => 'Laki-Laki',
                        ]
                    );

                    $updateIfChanged($ayah, 'nama_anggota_keluarga', $data['nama_ayah']);

                    $ayah->save();
                    $updateIfChanged($keluarga, 'nama_ayah', $data['nama_ayah']);
                }

                if (!empty($data['nik_ibu'])) {
                    $ibu = AnggotaKeluarga::firstOrCreate(
                        ['nik' => $data['nik_ibu']],
                        [
                            'nama_anggota_keluarga' => trim($data['nama_ibu']),
                            'keluarga_no_kk' => $data['no_kk'],
                            'posisi_keluarga' => 'Istri',
                            'jenis_kelamin' => 'Perempuan',
                        ]
                    );

                    $updateIfChanged($ibu, 'nama_anggota_keluarga', $data['nama_ibu']);

                    $ibu->save();
                    $updateIfChanged($keluarga, 'nama_ibu', $data['nama_ibu']);
                }

                $updateIfChanged($keluarga, 'nama_dusun', $data['nama_dusun'] ?? '');
                $updateIfChanged($keluarga, 'alamat', $data['alamat'] ?? '');

                if ($updated) {
                    DB::table('keluarga')->where('no_kk', $keluarga->no_kk)->update([
                        'nama_ayah' => $keluarga->nama_ayah,
                        'nama_ibu' => $keluarga->nama_ibu,
                        'nama_dusun' => $keluarga->nama_dusun,
                        'alamat' => $keluarga->alamat,
                        'updated_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data pasien berhasil ditambahkan.',
                'data' => $anggota
            ], 201);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menambahkan data pasien.',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }


    // ✏️ UPDATE data pasien
    public function update(Request $request, $nik)
    {
        try {
            $pasien = AnggotaKeluarga::where('nik', $nik)->firstOrFail();
            $validator = Validator::make($request->all(), [
                'nama_lengkap'    => 'required|string|max:255',
                'nik'             => 'required|string|max:20|unique:anggota_keluarga,nik,' . $pasien->nik . ',nik',
                'no_kk'           => 'required|exists:keluarga,no_kk',
                'jenis_kelamin'   => 'required|in:Laki-laki,Perempuan',
                'anak_ke'         => 'nullable|integer|min:1',
                'tinggi_lahir'    => 'nullable|integer|min:1',
                'tanggal_lahir'   => 'nullable|date',
                'penyakit_bawaan' => 'nullable|string|max:255',
                'berat_lahir'     => 'nullable|string|max:255',
                'bpjs'            => 'nullable|in:Ya,Tidak',
                'gakin'           => 'nullable|in:Ya,Tidak',
                'nama_ayah'       => 'nullable|string|max:255',
                'nama_ibu'        => 'nullable|string|max:255',
                'nik_ayah'        => 'nullable|string|max:255',
                'nik_ibu'         => 'nullable|string|max:255',
                'nomor_telp'      => 'nullable|string|max:20',
                'nama_dusun'      => 'nullable|string',
                'alamat'           => 'nullable|string|max:40',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validasi gagal.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            if (!empty($data['tanggal_lahir'])) {
                try {
                    $tgl = str_replace('/', '-', $data['tanggal_lahir']);
                    $data['tanggal_lahir'] = \Carbon\Carbon::createFromFormat('d-m-Y', $tgl)->format('Y-m-d');
                } catch (\Exception $e) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Format tanggal_lahir tidak valid. Gunakan format DD-MM-YYYY atau DD/MM/YYYY.',
                    ], 422);
                }
            }

            $bpjs  = $data['bpjs']  === 'Ya' ? true : ($data['bpjs']  === 'Tidak' ? false : null);
            $gakin = $data['gakin'] === 'Ya' ? true : ($data['gakin'] === 'Tidak' ? false : null);

            $oldNoKk = $pasien->keluarga_no_kk;
            $newNoKk = $data['no_kk'];

            DB::table('anggota_keluarga')
                ->where('nik', $nik)
                ->update([
                    'nama_anggota_keluarga' => trim($data['nama_lengkap']),
                    'nik'                    => $data['nik'],
                    'keluarga_no_kk'         => $newNoKk,
                    'nomor_telp'             => $data['nomor_telp'] ?? null,
                    'jenis_kelamin'          => $data['jenis_kelamin'],
                    'tanggal_lahir'          => $data['tanggal_lahir'] ?? null,
                    'Anak_Ke'                => $data['anak_ke'] ?? null,
                    'tinggi_lahir'           => $data['tinggi_lahir'] ?? null,
                    'penyakit_bawaan'        => trim($data['penyakit_bawaan'] ?? ''),
                    'berat_lahir'            => trim($data['berat_lahir'] ?? ''),
                    'bpjs'                   => $data['bpjs']  !== null ? $bpjs  : null,
                    'gakin'                  => $data['gakin'] !== null ? $gakin : null,
                    'updated_at'             => now(),
                ]);

            if ($oldNoKk !== $newNoKk) {
                DB::transaction(function () use ($oldNoKk, $newNoKk) {
                    DB::table('anggota_keluarga')
                        ->where('keluarga_no_kk', $oldNoKk)
                        ->update(['keluarga_no_kk' => $newNoKk, 'updated_at' => now()]);
                    DB::table('keluarga')
                        ->where('no_kk', $oldNoKk)
                        ->update(['no_kk' => $newNoKk, 'updated_at' => now()]);
                });
            }
            $keluarga = Keluarga::where('no_kk', $newNoKk)->first();

            if ($keluarga) {
                $updated = false;
                $updateIfChanged = function ($model, $attribute, $value) use (&$updated) {
                    if (!empty($value) && trim($value) !== trim($model->$attribute ?? '')) {
                        $model->$attribute = $value;
                        $updated = true;
                    }
                };

                if (!empty($data['nik_ayah'])) {
                    $ayahExists = AnggotaKeluarga::where('nik', $data['nik_ayah'])->exists();
                    if ($ayahExists) {
                        DB::table('anggota_keluarga')
                            ->where('nik', $data['nik_ayah'])
                            ->update([
                                'nama_anggota_keluarga' => trim($data['nama_ayah']),
                                'updated_at'            => now(),
                            ]);
                    } else {
                        DB::table('anggota_keluarga')->insert([
                            'nik'                   => $data['nik_ayah'],
                            'nama_anggota_keluarga' => trim($data['nama_ayah']),
                            'keluarga_no_kk'        => $newNoKk,
                            'posisi_keluarga'       => 'Kepala Keluarga',
                            'jenis_kelamin'         => 'Laki-Laki',
                            'created_at'            => now(),
                            'updated_at'            => now(),
                        ]);
                    }
                    $updateIfChanged($keluarga, 'nama_ayah', $data['nama_ayah']);
                }

                if (!empty($data['nik_ibu'])) {
                    $ibuExists = AnggotaKeluarga::where('nik', $data['nik_ibu'])->exists();
                    if ($ibuExists) {
                        DB::table('anggota_keluarga')
                            ->where('nik', $data['nik_ibu'])
                            ->update([
                                'nama_anggota_keluarga' => trim($data['nama_ibu']),
                                'updated_at'            => now(),
                            ]);
                    } else {
                        DB::table('anggota_keluarga')->insert([
                            'nik'                   => $data['nik_ibu'],
                            'nama_anggota_keluarga' => trim($data['nama_ibu']),
                            'keluarga_no_kk'        => $newNoKk,
                            'posisi_keluarga'       => 'Istri',
                            'jenis_kelamin'         => 'Perempuan',
                            'created_at'            => now(),
                            'updated_at'            => now(),
                        ]);
                    }
                    $updateIfChanged($keluarga, 'nama_ibu', $data['nama_ibu']);
                }

                $updateIfChanged($keluarga, 'nama_dusun', $data['nama_dusun'] ?? '');
                $updateIfChanged($keluarga, 'alamat',      $data['alamat']      ?? '');

                if ($updated) {
                    DB::table('keluarga')
                        ->where('no_kk', $newNoKk)
                        ->update([
                            'nama_ayah'   => $keluarga->nama_ayah,
                            'nama_ibu'    => $keluarga->nama_ibu,
                            'nama_dusun'  => $keluarga->nama_dusun,
                            'alamat'       => $keluarga->alamat,
                            'updated_at'  => now(),
                        ]);
                }
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pasien berhasil diperbarui.',
                'data'    => AnggotaKeluarga::where('nik', $data['nik'])->first(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data pasien: ' . $e->getMessage(),
            ], 500);
        }
    }



    // ❌ DELETE data pasien
    public function destroy($nik)
    {
        try {
            $anggota = AnggotaKeluarga::where('nik', $nik)->firstOrFail();
            $no_kk = $anggota->keluarga_no_kk;
            $updateData = [];

            if ($anggota->posisi_keluarga === 'Kepala Keluarga' && $anggota->nama_anggota_keluarga === $anggota->keluarga->nama_ayah) {
                $updateData['nama_ayah'] = null;
            } elseif ($anggota->posisi_keluarga === 'Istri' && $anggota->nama_anggota_keluarga === $anggota->keluarga->nama_ibu) {
                $updateData['nama_ibu'] = null;
            }

            if (!empty($updateData)) {
                Keluarga::where('no_kk', $no_kk)->update($updateData);
            }

            AnggotaKeluarga::where('nik', $nik)->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pasien berhasil dihapus.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pasien dengan NIK ' . $nik . ' tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
