
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\DosenNilaiController;
use App\Http\Controllers\MahasiswaDashboardController;
use App\Http\Controllers\MahasiswaNotificationController;
use App\Http\Controllers\MahasiswaPengumumanController;
use App\Http\Controllers\MahasiswaSearchController;
use App\Http\Controllers\ProdiJadwalController;
use App\Http\Controllers\ProdiDashboardController;
use App\Http\Controllers\ProdiMonitoringController;
use App\Http\Controllers\ProdiPengumumanController;
use App\Http\Controllers\ProdiTugasAkhirController;
use App\Http\Controllers\ProdiValidasiNilaiController;
use App\Http\Controllers\TugasAkhirController;
use App\Http\Controllers\TuProfileController;
use App\Http\Controllers\TuDocumentController;
use App\Http\Controllers\TuSuratKeputusanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::view('/forgot-password', 'auth.forgot-password')->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/verify-otp', fn () => redirect()->route('forgot-password'))->name('verify-otp');
    Route::get('/new-password', fn () => redirect()->route('forgot-password'))->name('new-password');
    Route::view('/logout/success', 'auth.logout-success')->name('logout.success');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');

    Route::get('/tugas-akhir', [TugasAkhirController::class, 'index'])->name('tugas-akhir');
    Route::post('/tugas-akhir', [TugasAkhirController::class, 'store'])->name('tugas-akhir.store');
    Route::get('/tugas-akhir/ajukan-judul', [TugasAkhirController::class, 'ajukanJudul'])->name('tugas-akhir.ajukan-judul');
    Route::get('/tugas-akhir/daftar-sempro', [TugasAkhirController::class, 'daftarSempro'])->name('tugas-akhir.daftar-sempro');
    Route::post('/tugas-akhir/daftar-sempro', [TugasAkhirController::class, 'ajukanSempro'])->name('tugas-akhir.daftar-sempro.store');
    Route::get('/tugas-akhir/daftar-sidang', [TugasAkhirController::class, 'daftarSidang'])->name('tugas-akhir.daftar-sidang');
    Route::post('/tugas-akhir/daftar-sidang', [TugasAkhirController::class, 'ajukanSidang'])->name('tugas-akhir.daftar-sidang.store');
    Route::get('/dokumen', [TugasAkhirController::class, 'dokumen'])->name('dokumen');
    Route::post('/dokumen/upload', [TugasAkhirController::class, 'uploadDokumen'])->name('dokumen.upload');
    Route::delete('/dokumen/{document}', [TugasAkhirController::class, 'hapusDokumen'])->name('dokumen.hapus');
    Route::get('/dokumen/{document}/download', [TugasAkhirController::class, 'downloadDokumen'])->name('dokumen.download');
    Route::get('/jadwal', [TugasAkhirController::class, 'jadwal'])->name('jadwal');
    Route::get('/profil', fn () => view('mahasiswa.pages.profil'))->name('profil');
    Route::put('/profil/data-diri', [AuthController::class, 'updateProfileData'])->name('profil.data-diri');
    Route::post('/profil/password', [AuthController::class, 'updatePassword'])->name('profil.password');
    Route::post('/profil/foto', [AuthController::class, 'updateProfilePhoto'])->name('profil.foto');
    Route::get('/cari', [MahasiswaSearchController::class, 'index'])->name('cari');
    Route::get('/pengumuman', [MahasiswaPengumumanController::class, 'index'])->name('pengumuman');
    Route::get('/notifikasi', [MahasiswaNotificationController::class, 'index'])->name('notifikasi');
});

Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [DosenDashboardController::class, 'index'])->name('dosen.dashboard');
    Route::get('/dosen/jadwal', [DosenDashboardController::class, 'jadwal'])->name('dosen.jadwal');
    Route::get('/dosen/jadwal/{jadwal}/nilai', [DosenNilaiController::class, 'edit'])->name('dosen.nilai');
    Route::post('/dosen/jadwal/{jadwal}/nilai', [DosenNilaiController::class, 'store'])->name('dosen.nilai.store');
    Route::get('/dosen/keterlibatan', [DosenDashboardController::class, 'keterlibatan'])->name('dosen.keterlibatan');
});

Route::middleware(['auth', 'role:prodi'])->group(function () {
    Route::get('/prodi/dashboard', [ProdiDashboardController::class, 'index'])->name('prodi.dashboard');
    Route::get('/prodi/progres-mahasiswa', [ProdiMonitoringController::class, 'progress'])->name('prodi.progres-mahasiswa');
    Route::get('/prodi/laporan', [ProdiMonitoringController::class, 'report'])->name('prodi.laporan');
    Route::get('/prodi/laporan/export', [ProdiMonitoringController::class, 'exportReport'])->name('prodi.laporan.export');
    Route::get('/prodi/rekap-dosen', [ProdiMonitoringController::class, 'lecturerInvolvement'])->name('prodi.rekap-dosen');
    Route::get('/prodi/validasi-nilai', [ProdiValidasiNilaiController::class, 'index'])->name('prodi.validasi-nilai');
    Route::get('/prodi/validasi-nilai/{jadwal}', [ProdiValidasiNilaiController::class, 'show'])->name('prodi.validasi-nilai.show');
    Route::patch('/prodi/validasi-nilai/{jadwal}/validate', [ProdiValidasiNilaiController::class, 'validateGrade'])->name('prodi.validasi-nilai.validate');
    Route::patch('/prodi/validasi-nilai/{jadwal}/return', [ProdiValidasiNilaiController::class, 'returnToLecturer'])->name('prodi.validasi-nilai.return');
    Route::get('/prodi/pengajuan-tugas-akhir', [ProdiTugasAkhirController::class, 'index'])->name('prodi.pengajuan-tugas-akhir');
    Route::get('/prodi/pengajuan-tugas-akhir/{tugasAkhir}/periksa', [ProdiTugasAkhirController::class, 'review'])->name('prodi.pengajuan-tugas-akhir.review');
    Route::get('/prodi/pengajuan-tugas-akhir/{tugasAkhir}/pembimbing', [ProdiTugasAkhirController::class, 'editPembimbing'])->name('prodi.pengajuan-tugas-akhir.pembimbing');
    Route::put('/prodi/pengajuan-tugas-akhir/{tugasAkhir}/pembimbing', [ProdiTugasAkhirController::class, 'assignPembimbing'])->name('prodi.pengajuan-tugas-akhir.pembimbing.update');
    Route::put('/prodi/pengajuan-tugas-akhir/{tugasAkhir}', [ProdiTugasAkhirController::class, 'update'])->name('prodi.pengajuan-tugas-akhir.update');
    Route::get('/prodi/sempro', [ProdiJadwalController::class, 'sempro'])->name('prodi.sempro');
    Route::get('/prodi/sidang', [ProdiJadwalController::class, 'sidang'])->name('prodi.sidang');
    Route::get('/prodi/jadwal', [ProdiJadwalController::class, 'index'])->name('prodi.jadwal');
    Route::post('/prodi/jadwal', [ProdiJadwalController::class, 'store'])->name('prodi.jadwal.store');
    Route::put('/prodi/jadwal/{jadwal}', [ProdiJadwalController::class, 'tetapkan'])->name('prodi.jadwal.tetapkan');
    Route::patch('/prodi/jadwal/{jadwal}/tolak', [ProdiJadwalController::class, 'tolak'])->name('prodi.jadwal.tolak');
    Route::patch('/prodi/jadwal/{jadwal}/selesai', [ProdiJadwalController::class, 'selesai'])->name('prodi.jadwal.selesai');
    Route::get('/prodi/pengumuman', [ProdiPengumumanController::class, 'index'])->name('prodi.pengumuman');
    Route::post('/prodi/pengumuman', [ProdiPengumumanController::class, 'store'])->name('prodi.pengumuman.store');
    Route::delete('/prodi/pengumuman/{pengumuman}', [ProdiPengumumanController::class, 'destroy'])->name('prodi.pengumuman.destroy');
});

Route::middleware(['auth', 'role:tu'])->group(function () {
    Route::get('/tu/dashboard', [TuSuratKeputusanController::class, 'dashboard'])->name('tu.dashboard');
    Route::get('/tu/dokumen', [TuDocumentController::class, 'index'])->name('tu.dokumen');
    Route::post('/tu/dokumen', [TuDocumentController::class, 'store'])->name('tu.dokumen.store');
    Route::get('/tu/dokumen/{document}/download', [TuDocumentController::class, 'download'])->name('tu.dokumen.download');
    Route::put('/tu/dokumen/{document}', [TuDocumentController::class, 'update'])->name('tu.dokumen.update');
    Route::delete('/tu/dokumen/{document}', [TuDocumentController::class, 'destroy'])->name('tu.dokumen.destroy');
    Route::get('/tu/administrasi-sk', [TuSuratKeputusanController::class, 'index'])->name('tu.administrasi-sk');
    Route::get('/tu/proses-sk/{jenis}/{id}', [TuSuratKeputusanController::class, 'process'])
        ->whereNumber('id')
        ->name('tu.proses-sk');
    Route::post('/tu/proses-sk/{jenis}/{id}', [TuSuratKeputusanController::class, 'issue'])
        ->whereNumber('id')
        ->name('tu.proses-sk.store');
    Route::get('/tu/profil', [TuProfileController::class, 'index'])->name('tu.profil');
    Route::post('/tu/profil/foto', [TuProfileController::class, 'updatePhoto'])->name('tu.profil.foto');
    Route::post('/tu/profil/password', [TuProfileController::class, 'updatePassword'])->name('tu.profil.password');
});

Route::middleware('auth')->group(function () {
    Route::get('/surat-keputusan/{jenis}/{id}/unduh', [TuSuratKeputusanController::class, 'download'])
        ->whereNumber('id')
        ->name('surat-keputusan.download');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
