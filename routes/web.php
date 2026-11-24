
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TugasAkhirController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenDashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::view('/forgot-password', 'auth.forgot-password')->name('forgot-password');
    Route::view('/verify-otp', 'auth.verify-otp')->name('verify-otp');
    Route::view('/new-password', 'auth.new-password')->name('new-password');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/tugas-akhir', [TugasAkhirController::class, 'index'])->name('tugas-akhir');
    Route::post('/tugas-akhir', [TugasAkhirController::class, 'store'])->name('tugas-akhir.store');
    Route::get('/tugas-akhir/ajukan-judul', [TugasAkhirController::class, 'ajukanJudul'])->name('tugas-akhir.ajukan-judul');
    Route::get('/tugas-akhir/daftar-sempro', [TugasAkhirController::class, 'daftarSempro'])->name('tugas-akhir.daftar-sempro');
    Route::get('/tugas-akhir/daftar-sidang', [TugasAkhirController::class, 'daftarSidang'])->name('tugas-akhir.daftar-sidang');
});

Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [DosenDashboardController::class, 'index'])->name('dosen.dashboard');
});

Route::middleware(['auth', 'role:prodi'])->group(function () {
    Route::view('/prodi/dashboard', 'prodi.dashboard')->name('prodi.dashboard');
});

Route::middleware(['auth', 'role:tu'])->group(function () {
    Route::view('/tu/dashboard', 'tu.dashboard')->name('tu.dashboard');
    Route::view('/tu/administrasi-sk', 'tu.administrasi-sk')->name('tu.administrasi-sk');
    Route::view('/tu/proses-sk', 'tu.proses-sk')->name('tu.proses-sk');
});

Route::middleware('auth')->group(function () {
    Route::view('/dokumen', 'pages.placeholder', [
        'title' => 'Dokumen',
        'description' => 'Halaman pengelolaan dokumen tugas akhir mahasiswa.'
    ])->name('dokumen');

    Route::view('/jadwal', 'pages.placeholder', [
        'title' => 'Jadwal',
        'description' => 'Halaman informasi jadwal bimbingan dan sidang.'
    ])->name('jadwal');

    Route::view('/profil', 'pages.placeholder', [
        'title' => 'Profil',
        'description' => 'Halaman informasi profil pengguna.'
    ])->name('profil');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
