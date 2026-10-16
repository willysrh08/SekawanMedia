<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BbmController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\RekapitulasiController;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Route;

// Rute Autentikasi / Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Halaman Utama / Root (Mengecek Session Login)
Route::get('/', function () {
    if (! session()->has('user_id')) {
        return redirect('/login');
    }

    return redirect('/dashboard');
});

// Shortcut Switch Role / Akun untuk Simulasi Cepat
Route::get('/dev-switch/{id}', function ($id) {
    $user = Pengguna::findOrFail($id);
    session([
        'user_id' => $user->id,
        'user_nama' => $user->nama,
        'user_email' => $user->email,
        'user_peran' => $user->peran,
        'wilayah_id' => $user->wilayah_id,
    ]);

    return redirect()->back()->with('success', 'Beralih peran ke: '.$user->nama.' ['.strtoupper($user->peran).']');
});

// Rute Halaman Dashboard Utama
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Rute Halaman Manajemen Pemesanan
Route::get('/pemesanan', [PemesananController::class, 'index'])->name('pemesanan.index');

// Rute Halaman Armada Tambang & CRUD Kendaraan
Route::get('/armada', [KendaraanController::class, 'index'])->name('armada.index');
Route::post('/armada', [KendaraanController::class, 'store']);
Route::post('/armada/store', [KendaraanController::class, 'store'])->name('armada.store');
Route::get('/armada/{id}', [KendaraanController::class, 'show'])->name('armada.show');
Route::match(['put', 'patch', 'post'], '/armada/{id}', [KendaraanController::class, 'update'])->name('armada.update');
Route::match(['delete', 'post'], '/armada/{id}/delete', [KendaraanController::class, 'destroy'])->name('armada.destroy.post');
Route::delete('/armada/{id}', [KendaraanController::class, 'destroy'])->name('armada.destroy');

// Rute CRUD Driver
Route::get('/driver', [DriverController::class, 'index'])->name('driver.index');
Route::post('/driver', [DriverController::class, 'store'])->name('driver.store');
Route::get('/driver/{id}', [DriverController::class, 'show'])->name('driver.show');
Route::put('/driver/{id}', [DriverController::class, 'update'])->name('driver.update');
Route::delete('/driver/{id}', [DriverController::class, 'destroy'])->name('driver.destroy');

// Rute Halaman Jadwal Driver & Servis (Resource Route Jadwal)
Route::resource('jadwal', JadwalController::class);

// Rute Halaman Konsumsi BBM & Odometer (CRUD BBM)
Route::get('/bbm', [BbmController::class, 'index'])->name('bbm.index');
Route::post('/bbm/store', [BbmController::class, 'store'])->name('bbm.store');
Route::get('/bbm/{id}', [BbmController::class, 'show'])->name('bbm.show');
Route::put('/bbm/{id}', [BbmController::class, 'update'])->name('bbm.update');
Route::delete('/bbm/{id}', [BbmController::class, 'destroy'])->name('bbm.destroy');

// Rute Halaman Ekspor Rekapitulasi & Unduh Laporan
Route::get('/rekapitulasi', [RekapitulasiController::class, 'index'])->name('rekapitulasi.index');
Route::get('/rekapitulasi/export', [RekapitulasiController::class, 'export'])->name('rekapitulasi.export');

// Endpoint Pemesanan & Approval Sesuai Ketentuan Technical Test
Route::post('/pemesanan/ajukan', [PemesananController::class, 'ajukan'])->name('pemesanan.ajukan');
Route::post('/pemesanan/{id}/admin-assign', [PemesananController::class, 'assignAdmin'])->name('pemesanan.assignAdmin');
Route::post('/approval/{persetujuanId}', [PemesananController::class, 'prosesApproval'])->name('approval.proses');
