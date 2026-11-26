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

        $dashboard = [
            'mahasiswa_baru' => [
                [
                    'nama' => 'Dia Febi Ayu',
                    'nim' => '2417020076',
                    'judul' => 'Rancang Bangun Sistem Informasi Tugas Akhir',
                ],
                [
                    'nama' => 'Elva Fitri',
                    'nim' => '2417020058',
                    'judul' => 'Analisis Kepuasan Pengguna Aplikasi Akademik',
                ],
                [
                    'nama' => 'Meutia Sandrina',
                    'nim' => '2417020096',
                    'judul' => 'Sistem Pendukung Keputusan Pemilihan Dosen',
                ],
                [
                    'nama' => 'Zoria Fitrioca',
                    'nim' => '2417020095',
                    'judul' => 'Implementasi E-Learning Berbasis Web',
                ],
            ],
            'jadwal_terdekat' => [
                [
                    'tanggal' => '2',
                    'bulan' => 'Des',
                    'kegiatan' => 'Sempro',
                    'nama' => 'Dia Febi Ayu',
                    'lokasi' => '09.00 Ruang Seminar 1',
                ],
                [
                    'tanggal' => '20',
                    'bulan' => 'Jul',
                    'kegiatan' => 'Sidang',
                    'nama' => 'Meutia Sandrina',
                    'lokasi' => '09.00 Ruang Sidang',
                ],
            ],
            'pengumuman' => [
                [
                    'judul' => 'Jadwal Sidang Tugas Akhir Gelombang 2',
                    'tanggal' => '15 Mei 2026',
                ],
                [
                    'judul' => 'Rekap Keterlibatan Dosen Periode Agustus',
                    'tanggal' => '10 Mei 2026',
                ],
            ],
        ];

        return view('dosen.dashboard', [
            'dashboard' => $dashboard,
            'mahasiswaSaya' => count($dashboard['mahasiswa_baru']),
            'pengujiSempro' => 0,
            'pengujiSidang' => 0,
            'jadwalHariIni' => 0,
            'mahasiswaTerbaru' => collect($dashboard['mahasiswa_baru']),
        ]);
    })->name('dosen.dashboard');

    Route::get('/dosen/mahasiswa-saya', function () {
        abort_unless(
            strtolower(trim((string) auth()->user()->role)) === 'dosen',
            403
        );

        return view('dosen.mahasiswa-saya');
    })->name('dosen.mahasiswa-saya');

    Route::get('/dosen/mahasiswa-saya/{nim}', function ($nim) {
        abort_unless(
            strtolower(trim((string) auth()->user()->role)) === 'dosen',
            403
        );

        $dataMahasiswa = [
            '2417020076' => [
                'nama' => 'Dia Febi Ayu',
                'nim' => '2417020076',
                'program_studi' => 'Sistem Informasi',
                'judul' => 'Rancang Bangun Sistem Informasi Tugas Akhir',
                'peran' => 'Pembimbing I',
                'status' => 'Sempro',
                'tahap' => 3,
                'pembimbing_1' => 'Dr. Yaslinda Lizar, S.Si., M.Kom.',
                'pembimbing_2' => 'Haditya Prasetyo, S.Kom, M.Kom',
            ],
            '2417020058' => [
                'nama' => 'Elva Fitri',
                'nim' => '2417020058',
                'program_studi' => 'Sistem Informasi',
                'judul' => 'Analisis Kepuasan Pengguna Aplikasi Akademik',
                'peran' => 'Pembimbing II',
                'status' => 'Pembimbing Ditentukan',
                'tahap' => 2,
                'pembimbing_1' => 'MHD ADHITIYA OKTA RIYANDI',
                'pembimbing_2' => 'Domi Sepri, M.Kom',
            ],
            '2417020096' => [
                'nama' => 'Meutia Sandrina',
                'nim' => '2417020096',
                'program_studi' => 'Sistem Informasi',
                'judul' => 'Sistem Pendukung Keputusan Pemilihan Dosen',
                'peran' => 'Pembimbing I',
                'status' => 'Sidang',
                'tahap' => 4,
                'pembimbing_1' => 'MHD ADHITIYA OKTA RIYANDI',
                'pembimbing_2' => 'Haditya Prasetyo, S.Kom, M.Kom',
            ],
            '2417020095' => [
                'nama' => 'Zoria Fitrioca',
                'nim' => '2417020095',
                'program_studi' => 'Sistem Informasi',
                'judul' => 'Implementasi E-Learning Berbasis Web',
                'peran' => 'Pembimbing II',
                'status' => 'Pengajuan Judul',
                'tahap' => 1,
                'pembimbing_1' => '',
                'pembimbing_2' => '',
            ],
        ];

        abort_unless(
            isset($dataMahasiswa[$nim]),
            404,
            'Mahasiswa tidak ditemukan.'
        );

        return view('dosen.detail-mahasiswa', [
            'mahasiswa' => $dataMahasiswa[$nim],
        ]);
    })->name('dosen.mahasiswa-saya.detail');

    Route::get('/dosen/jadwal', function () {
        abort_unless(
            strtolower(trim((string) auth()->user()->role)) === 'dosen',
            403
        );

        $jadwal = [
            [
                'kegiatan' => 'Seminar Proposal',
                'mahasiswa' => 'Dia Febi Ayu',
                'tanggal' => '2 Desember 2026, 09.00',
                'lokasi' => 'Ruang Seminar 1',
                'peran' => 'Pembimbing 1',
                'status' => 'Terjadwal',
                'jenis' => 'Seminar Proposal',
            ],
            [
                'kegiatan' => 'Seminar Proposal',
                'mahasiswa' => 'Nadia Putri',
                'tanggal' => '4 Juni 2026, 10.30',
                'lokasi' => 'Ruang Seminar 2',
                'peran' => 'Penguji',
                'status' => 'Terjadwal',
                'jenis' => 'Seminar Proposal',
            ],
            [
                'kegiatan' => 'Sidang/Komprehensif',
                'mahasiswa' => 'Meutia Sandrina',
                'tanggal' => '20 Juli 2026, 09.00',
                'lokasi' => 'Ruang Sidang',
                'peran' => 'Pembimbing 2',
                'status' => 'Terjadwal',
                'jenis' => 'sidang',
            ],
            [
                'kegiatan' => 'Sidang/Komprehensif',
                'mahasiswa' => 'Fajar Ramadhan',
                'tanggal' => '22 Juli 2026, 13.00',
                'lokasi' => 'Ruang Sidang',
                'peran' => 'Penguji',
                'status' => 'Terjadwal',
                'jenis' => 'sidang',
            ],
        ];

        return view('dosen.jadwal', compact('jadwal'));
    })->name('dosen.jadwal');

    Route::get('/dosen/input-nilai', function () {
        abort_unless(
            strtolower(trim((string) auth()->user()->role)) === 'dosen',
            403
        );

        $nilaiMahasiswa = [
            [
                'kegiatan' => 'Seminar Proposal',
                'mahasiswa' => 'Dia Febi Ayu',
                'tanggal' => '2 Desember 2026',
                'peran' => 'Pembimbing 1',
                'nilai' => null,
                'status' => 'Belum dinilai',
                'jenis' => 'Seminar Proposal',
            ],
            [
                'kegiatan' => 'Seminar Proposal',
                'mahasiswa' => 'Nadia Putri',
                'tanggal' => '4 Juni 2026',
                'peran' => 'Penguji',
                'nilai' => '84,93',
                'status' => 'Menunggu validasi',
                'jenis' => 'Seminar Proposal',
            ],
            [
                'kegiatan' => 'Sidang/Komprehensif',
                'mahasiswa' => 'Meutia Sandrina',
                'tanggal' => '20 Juli 2026',
                'peran' => 'Pembimbing 2',
                'nilai' => '87,67',
                'status' => 'Tervalidasi',
                'jenis' => 'sidang',
            ],
            [
                'kegiatan' => 'Sidang/Komprehensif',
                'mahasiswa' => 'Fajar Ramadhan',
                'tanggal' => '22 Juli 2026',
                'peran' => 'Penguji',
                'nilai' => '69,00',
                'status' => 'Dikembalikan',
                'jenis' => 'sidang',
            ],
        ];

        return view('dosen.input-nilai', [
            'nilaiMahasiswa' => $nilaiMahasiswa,
        ]);
    })->name('dosen.input-nilai');

    Route::get('/dosen/input-nilai/sempro/{nama}', function ($nama) {
        abort_unless(
            strtolower(trim((string) auth()->user()->role)) === 'dosen',
            403
        );

        $penilaian = [
            'nama' => urldecode($nama),
            'kegiatan' => 'Seminar Proposal',
            'tanggal' => '2 Juni 2026',
            'peran' => 'Pembimbing 1',
            'status' => 'Belum Dinilai',
        ];

        return view('dosen.input-nilai-sempro', compact('penilaian'));
    })->name('dosen.input-nilai.sempro');

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