<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Document;
use App\Models\Pengumuman;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DosenDashboardController extends Controller
{
    public function index()
    {
        $dosen = Auth::user();
        $jadwalHariIni = $this->schedulesFor($dosen)
            ->whereDate('waktu', today())
            ->count();
        $jadwalMendatang = $this->schedulesFor($dosen)
            ->with(['mahasiswa', 'tugasAkhir', 'penguji'])
            ->where('status', 'terjadwal')
            ->where('waktu', '>=', now())
            ->orderBy('waktu')
            ->take(6)
            ->get();
        $mahasiswaRows = $this->studentRows($dosen);
        $mahasiswaTerbaru = $mahasiswaRows->take(5);
        $jadwalMendatang->each(function (Jadwal $jadwal) use ($dosen) {
            $jadwal->peran_dosen = $this->scheduleRole($jadwal, $dosen);
        });
        $suratPembimbing = $this->adviseesFor($dosen)
            ->with('mahasiswa')
            ->whereNotNull('sk_pembimbing_file')
            ->get()
            ->map(fn (TugasAkhir $record) => [
                'id' => $record->id,
                'jenis' => 'pembimbing',
                'label' => 'SK Pembimbing',
                'mahasiswa' => $record->mahasiswa->name,
            ]);
        $suratPenguji = $this->examinerSchedulesFor($dosen)
            ->with('mahasiswa')
            ->whereNotNull('sk_tim_penguji_file')
            ->get()
            ->map(fn (Jadwal $record) => [
                'id' => $record->id,
                'jenis' => 'penguji',
                'label' => 'SK Tim Penguji',
                'mahasiswa' => $record->mahasiswa->name,
            ]);

        return view('dosen.dashboard', [
            'mahasiswaSaya' => $mahasiswaRows->count(),
            'pengujiSempro' => $this->examinerSchedulesFor($dosen)->where('jenis', 'sempro')->count(),
            'pengujiSidang' => $this->examinerSchedulesFor($dosen)->where('jenis', 'sidang')->count(),
            'jadwalHariIni' => $jadwalHariIni,
            'jadwalMendatang' => $jadwalMendatang,
            'mahasiswaTerbaru' => $mahasiswaTerbaru,
            'suratKeputusan' => $suratPembimbing->concat($suratPenguji),
            'pengumuman' => Pengumuman::latest()->take(2)->get(),
        ]);
    }

    public function jadwal(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:255',
            'jenis' => 'nullable|in:semua,sempro,sidang',
        ]);
        $query = $this->schedulesFor(Auth::user())
            ->with(['mahasiswa', 'tugasAkhir', 'penguji', 'dosenPenilai']);

        if (($filters['jenis'] ?? 'semua') !== 'semua') {
            $query->where('jenis', $filters['jenis']);
        }

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function (Builder $query) use ($search) {
                $query->whereHas('mahasiswa', function (Builder $query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nim', 'like', '%' . $search . '%');
                })->orWhereHas('tugasAkhir', function (Builder $query) use ($search) {
                    $query->where('judul', 'like', '%' . $search . '%');
                });
            });
        }

        $jadwal = $query->orderByRaw('CASE WHEN waktu IS NULL THEN 1 ELSE 0 END')
            ->orderBy('waktu')
            ->get();
        $jadwal->each(function (Jadwal $item) {
            $item->peran_dosen = $this->scheduleRole($item, Auth::user());
        });

        return view('dosen.jadwal', [
            'jadwal' => $jadwal,
            'search' => $filters['q'] ?? '',
            'jenis' => $filters['jenis'] ?? 'semua',
        ]);
    }

    public function mahasiswa(Request $request)
    {
        $filters = $request->validate([
            'jenis' => 'nullable|in:semua,pembimbing,penguji',
            'q' => 'nullable|string|max:255',
        ]);
        $dosen = Auth::user();
        $rows = $this->studentRows($dosen, $filters['jenis'] ?? 'semua', $filters['q'] ?? '');

        return view('dosen.mahasiswa', [
            'rows' => $rows,
            'jenis' => $filters['jenis'] ?? 'semua',
            'search' => $filters['q'] ?? '',
        ]);
    }

    public function detailMahasiswa(TugasAkhir $tugasAkhir)
    {
        $dosen = Auth::user();
        abort_unless($this->isInvolvedInTask($tugasAkhir, $dosen), 403);
        $tugasAkhir->load([
            'mahasiswa',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'jadwals' => fn ($query) => $query->with('penguji')->latest(),
        ]);
        $roles = $this->taskRoles($tugasAkhir, $dosen);
        $sempro = $tugasAkhir->jadwals->where('jenis', 'sempro')->first();
        $sidang = $tugasAkhir->jadwals->where('jenis', 'sidang')->first();
        $steps = [
            ['label' => 'Pengajuan Judul', 'done' => true],
            ['label' => 'Penetapan Pembimbing', 'done' => (bool) ($tugasAkhir->dosen_pembimbing_1 && $tugasAkhir->dosen_pembimbing_2)],
            ['label' => 'Sempro', 'done' => $sempro?->nilaiTervalidasi() && $sempro?->hasil === 'Lulus'],
            ['label' => 'Sidang/Komprehensif', 'done' => $sidang?->nilaiTervalidasi() && $sidang?->hasil === 'Lulus'],
        ];
        $suratKeputusan = collect();
        if (
            $tugasAkhir->sk_pembimbing_file
            && (in_array('Pembimbing I', $roles, true) || in_array('Pembimbing II', $roles, true))
        ) {
            $suratKeputusan->push([
                'label' => 'SK Pembimbing',
                'jenis' => 'pembimbing',
                'id' => $tugasAkhir->id,
            ]);
        }
        foreach ($tugasAkhir->jadwals as $jadwal) {
            if ($jadwal->sk_tim_penguji_file && $jadwal->penguji->contains('id', $dosen->id)) {
                $suratKeputusan->push([
                    'label' => 'SK Tim Penguji - ' . ($jadwal->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro'),
                    'jenis' => 'penguji',
                    'id' => $jadwal->id,
                ]);
            }
        }

        return view('dosen.detail-mahasiswa', compact('tugasAkhir', 'roles', 'steps', 'suratKeputusan'));
    }

    public function inputNilai()
    {
        $jadwal = $this->schedulesFor(Auth::user())
            ->where('status', 'selesai')
            ->with(['mahasiswa', 'tugasAkhir', 'penguji'])
            ->latest('waktu')
            ->get();
        $jadwal->each(function (Jadwal $item) {
            $item->peran_dosen = $this->scheduleRole($item, Auth::user());
        });

        return view('dosen.input-nilai', compact('jadwal'));
    }

    public function dokumen()
    {
        $dosen = Auth::user();
        $studentIds = $this->studentRows($dosen)->pluck('tugasAkhir.mahasiswa.id')->unique()->values();
        $documents = Document::with(['user', 'tugasAkhir'])
            ->whereIn('user_id', $studentIds)
            ->latest()
            ->get();
        $skPembimbing = $this->adviseesFor($dosen)
            ->with('mahasiswa')
            ->whereNotNull('sk_pembimbing_file')
            ->get()
            ->map(fn (TugasAkhir $task) => [
                'id' => $task->id,
                'jenis' => 'pembimbing',
                'label' => 'SK Pembimbing',
                'mahasiswa' => $task->mahasiswa->name,
                'nomor' => $task->sk_pembimbing_nomor,
            ]);
        $skPenguji = $this->examinerSchedulesFor($dosen)
            ->with('mahasiswa')
            ->whereNotNull('sk_tim_penguji_file')
            ->get()
            ->map(fn (Jadwal $item) => [
                'id' => $item->id,
                'jenis' => 'penguji',
                'label' => 'SK Tim Penguji',
                'mahasiswa' => $item->mahasiswa->name,
                'nomor' => $item->sk_tim_penguji_nomor,
            ]);

        return view('dosen.dokumen', [
            'documents' => $documents,
            'suratKeputusan' => $skPembimbing->concat($skPenguji),
        ]);
    }

    public function downloadDocument(Document $document)
    {
        $dosen = Auth::user();
        $allowed = $this->studentRows($dosen)
            ->contains(fn (array $row) => (int) $row['tugasAkhir']->user_id === (int) $document->user_id);
        abort_unless($allowed, 403);
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function profil()
    {
        return view('dosen.profil');
    }

    public function keterlibatan()
    {
        $dosen = Auth::user();
        $mahasiswaBimbingan = $this->adviseesFor($dosen)
            ->with('mahasiswa')
            ->latest()
            ->get();
        $pengujian = $this->examinerSchedulesFor($dosen)
            ->with(['mahasiswa', 'tugasAkhir'])
            ->latest()
            ->get();

        return view('dosen.keterlibatan', [
            'mahasiswaBimbingan' => $mahasiswaBimbingan,
            'pengujian' => $pengujian,
            'summary' => [
                'bimbingan' => $mahasiswaBimbingan->unique('user_id')->count(),
                'sempro' => $pengujian->where('jenis', 'sempro')->count(),
                'sidang' => $pengujian->where('jenis', 'sidang')->count(),
            ],
        ]);
    }

    private function adviseesFor(User $dosen): Builder
    {
        return TugasAkhir::where(function (Builder $query) use ($dosen) {
            $query->where('dosen_pembimbing_1', $dosen->id)
                ->orWhere('dosen_pembimbing_2', $dosen->id);
        });
    }

    private function examinerSchedulesFor(User $dosen): Builder
    {
        return Jadwal::whereIn('jenis', ['sempro', 'sidang'])
            ->where(function (Builder $query) use ($dosen) {
                $query->whereHas('penguji', fn (Builder $query) => $query->where('users.id', $dosen->id))
                    ->orWhere('dosen_penguji', $dosen->name);
            });
    }

    private function schedulesFor(User $dosen): Builder
    {
        return Jadwal::whereIn('jenis', ['sempro', 'sidang'])
            ->where(function (Builder $query) use ($dosen) {
                $query->whereHas('penguji', fn (Builder $query) => $query->where('users.id', $dosen->id))
                    ->orWhere('dosen_penguji', $dosen->name)
                    ->orWhereHas('tugasAkhir', function (Builder $query) use ($dosen) {
                        $query->where('dosen_pembimbing_1', $dosen->id)
                            ->orWhere('dosen_pembimbing_2', $dosen->id);
                    });
            });
    }

    private function studentRows(User $dosen, string $jenis = 'semua', string $search = '')
    {
        $examinerTaskIds = $this->examinerSchedulesFor($dosen)
            ->whereNotNull('tugas_akhir_id')
            ->distinct()
            ->pluck('tugas_akhir_id')
            ->all();
        $query = TugasAkhir::with([
            'mahasiswa',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'jadwals' => fn ($query) => $query->latest(),
        ]);

        if ($jenis === 'pembimbing') {
            $query->where(function (Builder $query) use ($dosen) {
                $query->where('dosen_pembimbing_1', $dosen->id)
                    ->orWhere('dosen_pembimbing_2', $dosen->id);
            });
        } elseif ($jenis === 'penguji') {
            $query->whereIn('id', $examinerTaskIds);
        } else {
            $query->where(function (Builder $query) use ($dosen, $examinerTaskIds) {
                $query->where('dosen_pembimbing_1', $dosen->id)
                    ->orWhere('dosen_pembimbing_2', $dosen->id)
                    ->orWhereIn('id', $examinerTaskIds);
            });
        }

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search) {
                $query->whereHas('mahasiswa', function (Builder $query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nim', 'like', '%' . $search . '%');
                })->orWhere('judul', 'like', '%' . $search . '%');
            });
        }

        return $query->latest()->get()->map(function (TugasAkhir $task) use ($dosen, $examinerTaskIds) {
            $roles = $this->taskRoles(
                $task,
                $dosen,
                in_array($task->id, $examinerTaskIds)
            );
            $sempro = $task->jadwals->where('jenis', 'sempro')->first();
            $sidang = $task->jadwals->where('jenis', 'sidang')->first();

            return [
                'tugasAkhir' => $task,
                'roles' => $roles,
                'status' => $this->studentStatus($task, $sempro, $sidang),
            ];
        })->values();
    }

    private function taskRoles(TugasAkhir $task, User $dosen, ?bool $isExaminer = null): array
    {
        $roles = [];
        if ((int) $task->dosen_pembimbing_1 === (int) $dosen->id) {
            $roles[] = 'Pembimbing I';
        }
        if ((int) $task->dosen_pembimbing_2 === (int) $dosen->id) {
            $roles[] = 'Pembimbing II';
        }
        $isExaminer ??= $this->examinerSchedulesFor($dosen)->where('tugas_akhir_id', $task->id)->exists();
        if ($isExaminer) {
            $roles[] = 'Penguji';
        }

        return $roles;
    }

    private function isInvolvedInTask(TugasAkhir $task, User $dosen): bool
    {
        return (int) $task->dosen_pembimbing_1 === (int) $dosen->id
            || (int) $task->dosen_pembimbing_2 === (int) $dosen->id
            || $this->examinerSchedulesFor($dosen)->where('tugas_akhir_id', $task->id)->exists();
    }

    private function studentStatus(TugasAkhir $task, ?Jadwal $sempro, ?Jadwal $sidang): string
    {
        if ($task->status !== 'Disetujui') {
            return 'Pengajuan Judul';
        }
        if (!$task->dosen_pembimbing_1 || !$task->dosen_pembimbing_2) {
            return 'Pembimbing Ditetapkan';
        }
        if ($sidang) {
            return 'Sidang';
        }
        if ($sempro) {
            return 'Sempro';
        }

        return 'Pembimbing Ditetapkan';
    }

    private function scheduleRole(Jadwal $jadwal, User $dosen): string
    {
        $roles = [];
        if ((int) $jadwal->tugasAkhir?->dosen_pembimbing_1 === (int) $dosen->id) {
            $roles[] = 'Pembimbing I';
        }
        if ((int) $jadwal->tugasAkhir?->dosen_pembimbing_2 === (int) $dosen->id) {
            $roles[] = 'Pembimbing II';
        }
        if (
            $jadwal->penguji->contains('id', $dosen->id)
            || trim((string) $jadwal->dosen_penguji) === $dosen->name
        ) {
            $roles[] = 'Penguji';
        }

        return implode(', ', $roles) ?: 'Pembimbing';
    }
}
