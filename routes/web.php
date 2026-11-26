<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// GUEST
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('forgot-password');

    Route::get('/verify-otp', function () {
        return view('auth.verify-otp');
    })->name('verify-otp');

    Route::get('/new-password', function () {
        return view('auth.new-password');
    })->name('new-password');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);
});

// USER LOGIN
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = strtolower(trim((string) auth()->user()->role));

        return match ($role) {
            'mahasiswa' => view('mahasiswa.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'prodi' => redirect()->route('prodi.dashboard'),
            'tu' => redirect()->route('tu.dashboard'),
            default => abort(403, 'Role akun tidak dikenali.'),
        };
    })->name('dashboard');

    Route::get('/dosen/dashboard', function () {
        abort_unless(
            strtolower(trim((string) auth()->user()->role)) === 'dosen',
            403,
            'Halaman ini hanya untuk dosen.'
        );

        return view('dosen.dashboard', [
            'mahasiswaSaya' => 0,
            'pengujiSempro' => 0,
            'pengujiSidang' => 0,
            'jadwalHariIni' => 0,
            'mahasiswaTerbaru' => collect(),
        ]);
    })->name('dosen.dashboard');
    Route::get('/dosen/mahasiswa-saya', function () {
    abort_unless(
        strtolower(trim((string) auth()->user()->role)) === 'dosen',
        403
    );
        return view('dosen.mahasiswa-saya');
        })->name('dosen.mahasiswa-saya');

    Route::get('/prodi/dashboard', function () {
        abort_unless(
            strtolower(trim((string) auth()->user()->role)) === 'prodi',
            403
        );

        return view('prodi.dashboard');
    })->name('prodi.dashboard');

    Route::get('/tu/dashboard', function () {
        abort_unless(
            strtolower(trim((string) auth()->user()->role)) === 'tu',
            403
        );

        return view('tu.dashboard');
    })->name('tu.dashboard');

    Route::view('/tugas-akhir', 'pages.placeholder', [
        'title' => 'Tugas Akhir',
        'description' => 'Halaman pengelolaan dan pemantauan tugas akhir mahasiswa.',
    ])->name('tugas-akhir');

    Route::view('/dokumen', 'pages.placeholder', [
        'title' => 'Dokumen',
        'description' => 'Halaman pengelolaan dokumen tugas akhir mahasiswa.',
    ])->name('dokumen');

    Route::view('/jadwal', 'pages.placeholder', [
        'title' => 'Jadwal',
        'description' => 'Halaman informasi jadwal bimbingan dan sidang.',
    ])->name('jadwal');

    Route::view('/profil', 'pages.placeholder', [
        'title' => 'Profil',
        'description' => 'Halaman informasi profil mahasiswa.',
    ])->name('profil');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});