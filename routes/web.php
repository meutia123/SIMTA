
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TugasAkhirController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect('/login');
});

// GUEST ROUTES (belum login)
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

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


    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);

});


// AUTH ROUTES (harus login)
Route::middleware('auth')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    // TUGAS AKHIR
    Route::get('/tugas-akhir', [TugasAkhirController::class, 'index'])
    ->name('tugas-akhir');
    Route::post('/tugas-akhir', [TugasAkhirController::class, 'store'])
    ->name('tugas-akhir.store');


    // DOKUMEN
    Route::view('/dokumen', 'pages.placeholder', [
        'title' => 'Dokumen',
        'description' => 'Halaman pengelolaan dokumen tugas akhir mahasiswa.'
    ])->name('dokumen');


    // JADWAL
    Route::view('/jadwal', 'pages.placeholder', [
        'title' => 'Jadwal',
        'description' => 'Halaman informasi jadwal bimbingan dan sidang.'
    ])->name('jadwal');


    // PROFIL
    Route::view('/profil', 'pages.placeholder', [
        'title' => 'Profil',
        'description' => 'Halaman informasi profil mahasiswa.'
    ])->name('profil');


    // HALAMAN TATA USAHA (TU)

    Route::view('/tu/dashboard', 'pages.tu.dashboard')
        ->name('tu.dashboard');

    Route::view('/tu/administrasi-sk', 'pages.tu.administrasi-sk')
        ->name('tu.administrasi-sk');

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

});
