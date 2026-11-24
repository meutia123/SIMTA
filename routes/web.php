
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TugasAkhirController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect('/login');
});

// GUEST ROUTES
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('forgot-password');

    Route::get('/verify-otp', function () {
        return view('auth.verify-otp');
    })->name('verify-otp');

    Route::get('/new-password', function () {
        return view('auth.new-password');
    })->name('new-password');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// AUTH ROUTES
Route::middleware('auth')->group(function () {

    // ROLE MAHASISWA
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::get('/tugas-akhir', [TugasAkhirController::class, 'index'])
            ->name('tugas-akhir');

        Route::post('/tugas-akhir', [TugasAkhirController::class, 'store'])
            ->name('tugas-akhir.store');

        Route::get('/tugas-akhir/ajukan-judul', [TugasAkhirController::class, 'ajukanJudul'])
            ->name('tugas-akhir.ajukan-judul');

        Route::get('/tugas-akhir/daftar-sempro', [TugasAkhirController::class, 'daftarSempro'])
            ->name('tugas-akhir.daftar-sempro');

        Route::get('/tugas-akhir/daftar-sidang', [TugasAkhirController::class, 'daftarSidang'])
            ->name('tugas-akhir.daftar-sidang');
    });

    // ROLE DOSEN
    Route::middleware('role:dosen')->group(function () {
        Route::get('/dosen/dashboard', function () {
            return view('pages.dosen.dashboard');
        })->name('dosen.dashboard');
    });

    // ROLE PRODI
    Route::middleware('role:prodi')->group(function () {
        Route::get('/prodi/dashboard', function () {
            return view('pages.prodi.dashboard');
        })->name('prodi.dashboard');
    });

    // ROLE TU
Route::middleware('role:tu')->group(function () {
    Route::get('/tu/dashboard', function () {
        return view('tu.dashboard');
    })->name('tu.dashboard');

    Route::view('/tu/administrasi-sk', 'tu.administrasi-sk')
        ->name('tu.administrasi-sk');

    Route::view('/tu/proses-sk', 'tu.proses-sk')
        ->name('tu.proses-sk');
});

    // HALAMAN BERSAMA UNTUK PENGGUNA YANG LOGIN
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

    // LOGOUT UNTUK SEMUA ROLE
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
