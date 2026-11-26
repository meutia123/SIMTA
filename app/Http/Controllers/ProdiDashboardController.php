<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;

class ProdiDashboardController extends Controller
{
    public function index()
    {
        $latestApplications = TugasAkhir::with([
            'mahasiswa',
            'dosenPembimbing1',
            'dosenPembimbing2',
        ])->latest()->take(4)->get();

        $statistics = [
            'judul_menunggu' => TugasAkhir::where('status', 'Menunggu')->count(),
            'belum_ada_pembimbing' => TugasAkhir::where('status', 'Disetujui')
                ->where(function ($query) {
                    $query->whereNull('dosen_pembimbing_1')
                        ->orWhereNull('dosen_pembimbing_2');
                })
                ->count(),
            'sempro_berjalan' => Jadwal::where('jenis', 'sempro')
                ->whereIn('status', ['menunggu', 'terjadwal'])
                ->count(),
            'sidang_berjalan' => Jadwal::where('jenis', 'sidang')
                ->whereIn('status', ['menunggu', 'terjadwal'])
                ->count(),
            'nilai_menunggu_validasi' => Jadwal::where('status_validasi_nilai', 'menunggu_validasi')->count(),
        ];

        $stageCounts = [
            'pengajuan' => TugasAkhir::count(),
            'pembimbing' => TugasAkhir::where('status', 'Disetujui')
                ->whereNotNull('dosen_pembimbing_1')
                ->whereNotNull('dosen_pembimbing_2')
                ->count(),
            'sempro' => Jadwal::where('jenis', 'sempro')
                ->whereIn('status', ['terjadwal', 'selesai'])
                ->distinct('user_id')
                ->count('user_id'),
            'sidang' => Jadwal::where('jenis', 'sidang')
                ->whereIn('status', ['terjadwal', 'selesai'])
                ->distinct('user_id')
                ->count('user_id'),
            'selesai' => Jadwal::where('jenis', 'sidang')
                ->where('status', 'selesai')
                ->where('hasil', 'Lulus')
                ->where(function ($query) {
                    $query->where('status_validasi_nilai', 'tervalidasi')
                        ->orWhereNull('status_validasi_nilai');
                })
                ->distinct('user_id')
                ->count('user_id'),
        ];
        $maxStageCount = max(1, ...array_values($stageCounts));

        return view('prodi.dashboard', [
            'latestApplications' => $latestApplications,
            'statistics' => $statistics,
            'stageCounts' => $stageCounts,
            'maxStageCount' => $maxStageCount,
            'studentCount' => User::where('role', 'mahasiswa')->count(),
        ]);
    }
}
