<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Notifications\MahasiswaNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProdiValidasiNilaiController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => 'nullable|in:semua,menunggu_validasi,tervalidasi,dikembalikan',
        ]);
        $status = $filters['status'] ?? 'semua';
        $query = $this->gradeQuery()->with(['mahasiswa', 'tugasAkhir', 'dosenPenilai']);

        if ($status === 'tervalidasi') {
            $query->where(function ($query) {
                $query->where('status_validasi_nilai', 'tervalidasi')
                    ->orWhere(function ($query) {
                        $query->whereNull('status_validasi_nilai')
                            ->whereNotNull('nilai')
                            ->whereNotNull('hasil');
                    });
            });
        } elseif ($status !== 'semua') {
            $query->where('status_validasi_nilai', $status);
        }

        return view('prodi.validasi-nilai', [
            'jadwal' => $query->latest()->get(),
            'status' => $status,
            'counts' => [
                'semua' => $this->gradeQuery()->count(),
                'menunggu_validasi' => $this->gradeQuery()->where('status_validasi_nilai', 'menunggu_validasi')->count(),
                'tervalidasi' => $this->gradeQuery()->where(function ($query) {
                    $query->where('status_validasi_nilai', 'tervalidasi')
                        ->orWhere(function ($query) {
                            $query->whereNull('status_validasi_nilai')
                                ->whereNotNull('nilai')
                                ->whereNotNull('hasil');
                        });
                })->count(),
                'dikembalikan' => $this->gradeQuery()->where('status_validasi_nilai', 'dikembalikan')->count(),
            ],
        ]);
    }

    public function show(Jadwal $jadwal)
    {
        abort_unless(
            in_array($jadwal->jenis, ['sempro', 'sidang'], true)
            && $jadwal->status === 'selesai'
            && $jadwal->nilai !== null,
            404
        );

        $jadwal->load(['mahasiswa', 'tugasAkhir', 'dosenPenilai']);

        return view('prodi.periksa-nilai', compact('jadwal'));
    }

    public function validateGrade(Jadwal $jadwal)
    {
        abort_unless(
            in_array($jadwal->jenis, ['sempro', 'sidang'], true)
            && $jadwal->status === 'selesai'
            && $jadwal->status_validasi_nilai === 'menunggu_validasi',
            403
        );

        $jadwal->update([
            'status_validasi_nilai' => 'tervalidasi',
            'catatan_validasi_nilai' => null,
        ]);
        $jadwal->mahasiswa->notify(new MahasiswaNotification(
            'Nilai ' . $this->examName($jadwal) . ' telah divalidasi Prodi.',
            route($jadwal->jenis === 'sempro' ? 'tugas-akhir.daftar-sempro' : 'tugas-akhir.daftar-sidang')
        ));

        return redirect()->route('prodi.validasi-nilai.show', $jadwal)
            ->with('success', 'Nilai telah divalidasi dan menjadi nilai resmi.');
    }

    public function returnToLecturer(Request $request, Jadwal $jadwal)
    {
        abort_unless(
            in_array($jadwal->jenis, ['sempro', 'sidang'], true)
            && $jadwal->status === 'selesai'
            && $jadwal->status_validasi_nilai === 'menunggu_validasi',
            403
        );
        $validated = $request->validate([
            'catatan_validasi_nilai' => 'required|string|max:2000',
        ]);

        $jadwal->update([
            'status_validasi_nilai' => 'dikembalikan',
            'catatan_validasi_nilai' => $validated['catatan_validasi_nilai'],
        ]);

        return redirect()->route('prodi.validasi-nilai.show', $jadwal)
            ->with('success', 'Nilai dikembalikan kepada dosen penilai untuk diperbaiki.');
    }

    private function examName(Jadwal $jadwal): string
    {
        return $jadwal->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro';
    }

    private function gradeQuery(): Builder
    {
        return Jadwal::whereIn('jenis', ['sempro', 'sidang'])
            ->where('status', 'selesai')
            ->where(function ($query) {
                $query->whereNotNull('status_validasi_nilai')
                    ->orWhere(function ($query) {
                        $query->whereNotNull('nilai')->whereNotNull('hasil');
                    });
            });
    }
}
