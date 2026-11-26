<?php

namespace App\Http\Controllers;

use App\Models\TugasAkhir;
use App\Models\User;
use App\Notifications\MahasiswaNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProdiTugasAkhirController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => 'nullable|in:semua,Menunggu,Disetujui,Perlu Perbaikan,Ditolak',
            'q' => 'nullable|string|max:255',
        ]);

        $query = TugasAkhir::with([
            'mahasiswa',
            'calonDosen1',
            'calonDosen2',
            'calonDosen3',
            'dosenPembimbing1',
            'dosenPembimbing2',
        ]);

        if (($filters['status'] ?? 'semua') !== 'semua') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function ($query) use ($search) {
                $query->where('judul', 'like', '%' . $search . '%')
                    ->orWhereHas('mahasiswa', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nim', 'like', '%' . $search . '%');
                    });
            });
        }

        $pengajuan = $query->latest()->get();
        $statusCounts = TugasAkhir::query()
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        return view('prodi.pengajuan-tugas-akhir', [
            'pengajuan' => $pengajuan,
            'filters' => [
                'status' => $filters['status'] ?? 'semua',
                'q' => $filters['q'] ?? '',
            ],
            'statusCounts' => $statusCounts,
        ]);
    }

    public function review(TugasAkhir $tugasAkhir)
    {
        $tugasAkhir->load(['mahasiswa', 'calonDosen1', 'calonDosen2', 'calonDosen3', 'dosenPembimbing1', 'dosenPembimbing2']);

        return view('prodi.periksa-judul', [
            'tugasAkhir' => $tugasAkhir,
            'judulSerupa' => $this->findSimilarTitles($tugasAkhir),
        ]);
    }

    public function editPembimbing(TugasAkhir $tugasAkhir)
    {
        abort_unless($tugasAkhir->status === 'Disetujui', 404);

        $tugasAkhir->load(['mahasiswa', 'calonDosen1', 'calonDosen2', 'calonDosen3']);
        $dosen = User::where('role', 'dosen')->orderBy('name')->get();
        $suggestedIds = collect([
            $tugasAkhir->calon_dosen_1,
            $tugasAkhir->calon_dosen_2,
            $tugasAkhir->calon_dosen_3,
        ])->filter()->unique()->values();
        $rekomendasi = $dosen->whereIn('id', $suggestedIds)
            ->sortBy(fn (User $item) => $suggestedIds->search($item->id))
            ->values();

        return view('prodi.tetapkan-pembimbing', compact('tugasAkhir', 'dosen', 'rekomendasi'));
    }

    public function update(Request $request, TugasAkhir $tugasAkhir)
    {
        if ($tugasAkhir->status !== 'Menunggu') {
            return back()->withErrors('Pengajuan ini tidak lagi menunggu pemeriksaan.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Disetujui,Perlu Perbaikan,Ditolak',
            'dosen_pembimbing_1' => 'nullable|exists:users,id',
            'dosen_pembimbing_2' => 'nullable|different:dosen_pembimbing_1|exists:users,id',
            'catatan_prodi' => 'required_if:status,Perlu Perbaikan|nullable|string|max:2000',
        ]);

        if (
            !empty($validated['dosen_pembimbing_1'])
            !== !empty($validated['dosen_pembimbing_2'])
        ) {
            return back()->withErrors('Pilih kedua dosen pembimbing atau kosongkan keduanya.')->withInput();
        }

        if ($validated['status'] === 'Disetujui' && !empty($validated['dosen_pembimbing_1'])) {
            $this->ensureSelectedAdvisorsAreDosen($validated);
        }

        $changes = [
            'status' => $validated['status'],
            'catatan_prodi' => $validated['catatan_prodi'] ?? null,
            'dosen_pembimbing_1' => $validated['dosen_pembimbing_1'] ?? null,
            'dosen_pembimbing_2' => $validated['dosen_pembimbing_2'] ?? null,
        ];

        if (
            $validated['status'] === 'Disetujui'
            && !empty($changes['dosen_pembimbing_1'])
            && ! $tugasAkhir->sk_pembimbing_diminta_at
        ) {
            $changes['sk_pembimbing_diminta_at'] = now();
        }

        $tugasAkhir->update($changes);
        $this->notifyStudentOfReview($tugasAkhir, $validated['status']);

        return redirect()->route('prodi.pengajuan-tugas-akhir')
            ->with('success', 'Hasil pemeriksaan judul berhasil disimpan.');
    }

    public function assignPembimbing(Request $request, TugasAkhir $tugasAkhir)
    {
        abort_unless($tugasAkhir->status === 'Disetujui', 404);

        $validated = $request->validate([
            'dosen_pembimbing_1' => 'required|different:dosen_pembimbing_2|exists:users,id',
            'dosen_pembimbing_2' => 'required|different:dosen_pembimbing_1|exists:users,id',
        ]);
        $this->ensureSelectedAdvisorsAreDosen($validated);

        $changes = [
            ...$validated,
            'sk_pembimbing_diminta_at' => $tugasAkhir->sk_pembimbing_diminta_at ?? now(),
        ];
        $tugasAkhir->update($changes);
        $tugasAkhir->mahasiswa->notify(new MahasiswaNotification(
            'Dosen pembimbing tugas akhir telah ditetapkan.',
            route('tugas-akhir')
        ));

        return redirect()->route('prodi.pengajuan-tugas-akhir')
            ->with('success', 'Dosen pembimbing berhasil ditetapkan.');
    }

    private function ensureSelectedAdvisorsAreDosen(array $validated): void
    {
        foreach (['dosen_pembimbing_1', 'dosen_pembimbing_2'] as $field) {
            if (
                isset($validated[$field])
                && !User::where('id', $validated[$field])->where('role', 'dosen')->exists()
            ) {
                abort(422, 'Pembimbing harus dipilih dari akun dosen.');
            }
        }
    }

    private function findSimilarTitles(TugasAkhir $current): Collection
    {
        $normalize = fn (string $title) => preg_replace(
            '/[^\pL\pN\s]/u',
            ' ',
            mb_strtolower($title)
        );
        $currentTitle = preg_replace('/\s+/', ' ', trim($normalize($current->judul)));
        $matches = collect();

        TugasAkhir::with('mahasiswa')
            ->where('id', '!=', $current->id)
            ->get()
            ->each(function (TugasAkhir $item) use ($currentTitle, $normalize, $matches) {
                $candidateTitle = preg_replace('/\s+/', ' ', trim($normalize($item->judul)));
                similar_text($currentTitle, $candidateTitle, $similarity);

                if ($similarity >= 80) {
                    $matches->push([
                        'judul' => $item->judul,
                        'mahasiswa' => $item->mahasiswa->name,
                        'similarity' => round($similarity),
                    ]);
                }
            });

        return $matches->sortByDesc('similarity')->take(5)->values();
    }

    private function notifyStudentOfReview(TugasAkhir $tugasAkhir, string $status): void
    {
        $message = $status === 'Perlu Perbaikan'
            ? 'Pengajuan judul perlu diperbaiki: ' . $tugasAkhir->catatan_prodi
            : ($status === 'Disetujui'
                ? 'Judul tugas akhir telah disetujui.'
                : 'Pengajuan judul tugas akhir ditolak.');

        $tugasAkhir->mahasiswa->notify(new MahasiswaNotification(
            $message,
            route('tugas-akhir')
        ));
    }
}
