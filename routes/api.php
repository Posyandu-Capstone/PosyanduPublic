<?php

use App\Http\Controllers\AdminVerifikator\AdminVerifikatorController;
use App\Http\Controllers\AdminVerifikator\WargaVerifikatorController;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Authentication\AuthWargaController;
use App\Http\Controllers\Authentication\ForgotPasswordController;
use App\Http\Controllers\Authentication\GoogleAuthController;
use App\Http\Controllers\Keluarga\AnggotaKeluargaController;
use App\Http\Controllers\Keluarga\KeluargaController;
use App\Http\Controllers\Laporan\LaporanController;
use App\Http\Controllers\Location\DesaController;
use App\Http\Controllers\Location\DusunController;
use App\Http\Controllers\Location\KecamatanController;
use App\Http\Controllers\Location\RtController;
use App\Http\Controllers\Location\RwController;
use App\Http\Controllers\Mobile\PortalBeritaController;
use App\Http\Controllers\Mobile\PortalEKmsController;
use App\Http\Controllers\Mobile\PortalPemeriksaanController;
use App\Http\Controllers\Mobile\ProfilAnggotaKeluargaController;
use App\Http\Controllers\News\BeritaController;
use App\Http\Controllers\Pemeriksaan\LaporanKesehatanController;
use App\Http\Controllers\Pemeriksaan\PemeriksaanController;
use App\Http\Controllers\Posyandu\DataPosyanduController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

// User Authentication
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('NIK', [AuthController::class, 'checkNIK']);
    Route::get('user', [AuthController::class, 'getUserByEmail']);
    Route::patch('update-nik', [AuthController::class, 'updateNik']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('lengkapi-data', [AuthController::class, 'showLengkapiData'])->name('lengkapi-data');
    Route::patch('update', [AuthController::class, 'updateProfile']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);
});

// Warga Authentication
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('warga/login', [AuthWargaController::class, 'login']);
    Route::post('warga/register', [AuthWargaController::class, 'register']);
    Route::post('warga/logout', [AuthWargaController::class, 'logout']);
    Route::post('warga/refresh', [AuthWargaController::class, 'refresh']);
    Route::post('warga/me', [AuthWargaController::class, 'me']);
    Route::post('warga/anggota', [AuthWargaController::class, 'anggotaKeluarga']);
});

// Dashboard API
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::get('/search', [DashboardController::class, 'getSearchPosyandu']);
    Route::get('/poskoAllow', [DashboardController::class, 'getAllowedPoskoPosyanduAll'])->middleware('role:Admin Desa,Kader');
    Route::get('/posko', [DashboardController::class, 'getPoskoPosyanduAll'])->middleware('role:Admin Desa,Kader');
    Route::get('/poskos', [DashboardController::class, 'getNamaPosyanduOnly']);
    Route::get('/hari', [DashboardController::class, 'getHariIni']);
    Route::get('/statistics', [DashboardController::class, 'getStatistics'])->middleware('role:Admin Desa,Kader');
    Route::get('/statisticsBalita', [DashboardController::class, 'getDataBalita'])->middleware('role:Admin Desa,Kader');
    Route::get('/posyanduList', [DashboardController::class, 'getPosyanduList'])->middleware('role:Admin Desa');
});

Route::prefix('auth')->middleware(['api', 'role:Admin Desa,Kader'])->group(function () {
    Route::apiResource('kecamatan', KecamatanController::class);
    Route::apiResource('desa', DesaController::class);
    Route::apiResource('dusun', DusunController::class);
    Route::apiResource('rw', RWController::class);
    Route::apiResource('rt', RTController::class);
});

Route::prefix('auth')->middleware(['api', 'role:Admin Desa'])->group(function () {
    Route::controller(DataPosyanduController::class)->group(function () {
        Route::get('/posyandu/search', 'getSearchPosyandu');
        Route::get('/posyandu/{id}', 'showPosyandu');
        Route::get('/posyandu', 'getPosyandu');
        Route::post('/posyandu', 'addPosyandu');
        Route::put('/posyandu/{id}', 'updatePosyandu');
        Route::delete('/posyandu/{id}', 'destroyPosyandu');
    });
});

Route::prefix('auth')->middleware(['api', 'role:Admin Desa'])->group(function () {
    Route::apiResource('warga/keluarga', KeluargaController::class);
    Route::apiResource('warga/anggota-keluarga', AnggotaKeluargaController::class);
});

Route::prefix('auth')->middleware(['api', 'role:Admin Desa,Kader'])->group(function () {
    Route::apiResource('laporan-kesehatan', LaporanKesehatanController::class);
    Route::get('laporan/search', [LaporanKesehatanController::class, 'search']);
});
Route::prefix('auth')->middleware(['api', 'role:Admin Desa,Kader'])->group(function () {
    Route::get('laporan', [LaporanController::class, 'showLaporan']);
});
Route::prefix('auth')->middleware('api')->group(function () {
    // Route::apiResource('/pemeriksaan', PemeriksaanController::class);
    Route::get('/antrian/{berita_id}', [PemeriksaanController::class, 'getByBeritaId'])->middleware('role:Admin Desa,Kader');
    Route::get('/pemeriksaan', [PemeriksaanController::class, 'index'])->middleware('role:Admin Desa,Kader');
    Route::get('/riwayat_pemeriksaan/{id}', [PemeriksaanController::class, 'show'])->middleware('role:Admin Desa,Kader');
    Route::post('/pemeriksaan', [PemeriksaanController::class, 'store'])->middleware('role:Admin Desa,Kader');
    Route::patch('/pemeriksaan/{id}', [PemeriksaanController::class, 'update'])->middleware('role:Kader');
    Route::get('pemeriksaan/search', [PemeriksaanController::class, 'search'])->middleware('role:Admin Desa,Kader');
    Route::get('/pemeriksaan/{id}', [PemeriksaanController::class, 'riwayatPemeriksaanBalita'])->middleware('role:Admin Desa,Kader');
    Route::get('/pemeriksaan_balita/{id}', [PemeriksaanController::class, 'riwayatPemeriksaan'])->middleware('role:Admin Desa,Kader');
    Route::delete('pemeriksaan/{id}', [PemeriksaanController::class, 'destroy']);
});

Route::prefix('auth')->middleware(['api', 'role:Admin Verifikator'])->group(function () {
    Route::apiResource('verifikator', AdminVerifikatorController::class);
    Route::apiResource('warga-verifikator', WargaVerifikatorController::class);
});
Route::prefix('auth')->middleware(['api', 'role:Kader'])->group(function () {
    Route::apiResource('berita', BeritaController::class)->parameters([
        'berita' => 'berita'
    ]);
});

Route::prefix('auth')->middleware(['api', 'role:Admin Desa,Kader,Admin Verifikator'])->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', [ProfileController::class, 'showProfile']);
        Route::put('/profile', [ProfileController::class, 'updateProfile']);
        Route::post('/profile/password', [ProfileController::class, 'updatePassword']);
    });
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::get('warga/show/{wargaId}/{anggota_nik}/{tipePemeriksaan}', [PortalPemeriksaanController::class, 'show']);
    Route::get('warga/get/{id}', [PortalPemeriksaanController::class, 'get']);
    Route::get('warga/anggota/{keluarga_no_kk}',  [PortalPemeriksaanController::class, 'anggota']);
    Route::get('warga/berita/{no_kk}', [PortalBeritaController::class, 'getBerita']);
    Route::get('warga/berita-anggota/{no_kk}', [PortalBeritaController::class, 'getAnggota']);
    Route::get('warga/berita-detail/{posyandu_id}', [PortalBeritaController::class, 'getDetail']);
    Route::get('warga/berita-detail/{berita_id}/{warga_id}', [PortalBeritaController::class, 'getAnggotaAntrian']);
    Route::get('warga/berita-antrian/{berita_id}', [PortalBeritaController::class, 'getNomorAntrianBerikutnya']);
    Route::post('warga/berita-daftar', [PortalBeritaController::class, 'daftarAntrian']);
    Route::patch('warga/update-profil/{id}', [AuthWargaController::class, 'updateProfile']);
    Route::get('warga/update-profil/{id}', [AuthWargaController::class, 'getProfile']);
    Route::patch('warga/update-email/{id}', [AuthWargaController::class, 'updateEmail']);
    Route::patch('warga/update-password/{id}', [AuthWargaController::class, 'updatePassword']);
    Route::get('warga/ekms/data/{nik}/{definisi}', [PortalEKmsController::class, 'getData']);
    Route::get('warga/ekms/data/{nik}', [PortalEKmsController::class, 'getPerkembangan']);
    Route::get('warga/ekms/data-diri/{nik}', [PortalEKmsController::class, 'getDataDiri']);
    Route::post('warga/lupa-password', [AuthWargaController::class, 'forgetPassword']);
    Route::get('password/reset-now', [AuthWargaController::class, 'resetNow']);
    Route::get('warga/profil/anggota/{nik}', [ProfilAnggotaKeluargaController::class, 'getDetailAnggota']);
    Route::patch('warga/profil/anggota/{nik}', [ProfilAnggotaKeluargaController::class, 'updateAnggota']);
    Route::get('warga/posko', [AuthWargaController::class, 'getPosyanduList']);
    Route::get('warga/posyandu/{no_kk}', [ProfilAnggotaKeluargaController::class, 'getPosyandu']);
});
