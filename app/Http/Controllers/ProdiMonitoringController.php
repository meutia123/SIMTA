<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class ProdiMonitoringController extends Controller
{
    public function progress(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:255',
            'tahap' => 'nullable|in:pengajuan-judul,pembimbing,sempro,sidang,selesai',
        ]);
        $rows = $this->progressRows();

        if (!empty($filters['q'])) {
            $search = mb_strtolower(trim($filters['q']));
            $rows = $rows->filter(function (array $row) use ($search) {
                $student = $row['tugasAkhir']->mahasiswa;

                return str_contains(mb_strtolower($row['tugasAkhir']->judul), $search)
                    || str_contains(mb_strtolower($student->name), $search)
                    || str_contains(mb_strtolower((string) $student->nim), $search);
            });
        }

        if (!empty($filters['tahap'])) {
            $rows = $rows->where('tahap_key', $filters['tahap']);
        }

        return view('prodi.progres-mahasiswa', [
            'rows' => $rows->values(),
            'filters' => [
                'q' => $filters['q'] ?? '',
                'tahap' => $filters['tahap'] ?? 'semua',
            ],
        ]);
    }

    public function report(Request $request)
    {
        $allRows = $this->progressRows();
        $periods = $this->reportPeriods($allRows);
        $filters = $request->validate([
            'periode' => ['nullable', 'date_format:Y-m', Rule::in($periods->keys()->all())],
        ]);
        $periode = $filters['periode'] ?? $periods->keys()->first();
        $rows = $periode
            ? $allRows->filter(fn (array $row) => $this->periodForRow($row) === $periode)
            : $allRows;
        $stageLabels = [
            'pengajuan-judul' => 'Pengajuan Judul',
            'pembimbing' => 'Pembimbing Ditetapkan',
            'sempro' => 'Sempro',
            'sidang' => 'Sidang / Komprehensif',
            'selesai' => 'Selesai',
        ];
        $stageCounts = collect($stageLabels)->mapWithKeys(fn (string $label, string $key) => [
            $key => [
                'label' => $label,
                'count' => $rows->where('tahap_key', $key)->count(),
            ],
        ]);
        $maxStageCount = max(1, (int) $stageCounts->max('count'));

        return view('prodi.laporan', [
            'summary' => [
                'total' => $rows->count(),
                'disetujui' => $rows->where('tugasAkhir.status', 'Disetujui')->count(),
                'sempro_lulus' => $rows->filter(fn (array $row) => $row['sempro']?->nilaiTervalidasi() && $row['sempro']?->hasil === 'Lulus')->count(),
                'sidang_lulus' => $rows->filter(fn (array $row) => $row['sidang']?->nilaiTervalidasi() && $row['sidang']?->hasil === 'Lulus')->count(),
            ],
            'stageCounts' => $stageCounts,
            'maxStageCount' => $maxStageCount,
            'periods' => $periods,
            'periode' => $periode,
        ]);
    }

    public function exportReport(Request $request)
    {
        $allRows = $this->progressRows();
        $periods = $this->reportPeriods($allRows);
        $filters = $request->validate([
            'periode' => ['nullable', 'date_format:Y-m', Rule::in($periods->keys()->all())],
        ]);
        $periode = $filters['periode'] ?? $periods->keys()->first();
        $rows = $periode
            ? $allRows->filter(fn (array $row) => $this->periodForRow($row) === $periode)
            : $allRows;
        $csvRows = $rows->map(function (array $row) {
            $task = $row['tugasAkhir'];
            $student = $task->mahasiswa;

            return [
                $student->nim,
                $student->name,
                $task->judul,
                $task->status,
                $task->dosenPembimbing1?->name,
                $task->dosenPembimbing2?->name,
                $row['sempro']?->status,
                $row['sempro']?->nilaiTervalidasi() ? $row['sempro']->hasil : null,
                $row['sempro']?->nilaiTervalidasi() ? $row['sempro']->nilai : null,
                $row['sidang']?->status,
                $row['sidang']?->nilaiTervalidasi() ? $row['sidang']->hasil : null,
                $row['sidang']?->nilaiTervalidasi() ? $row['sidang']->nilai : null,
                $row['progress'],
            ];
        })->map(fn (array $values) => array_map(function ($value) {
            $value = (string) ($value ?? '');

            return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
        }, $values))->all();

        return response()->streamDownload(function () use ($csvRows) {
            $stream = fopen('php://output', 'wb');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, [
                'NIM',
                'Mahasiswa',
                'Judul',
                'Status Pengajuan',
                'Pembimbing 1',
                'Pembimbing 2',
                'Status Sempro',
                'Hasil Sempro',
                'Nilai Sempro',
                'Status Sidang/Komprehensif',
                'Hasil Sidang/Komprehensif',
                'Nilai Sidang/Komprehensif',
                'Progres',
            ]);

            foreach ($csvRows as $row) {
                fputcsv($stream, $row);
            }

            fclose($stream);
        }, 'laporan-progres-tugas-akhir' . ($periode ? '-' . $periode : '') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function lecturerInvolvement(Request $request)
    {
        $monthNames = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $periods = Jadwal::whereIn('jenis', ['sempro', 'sidang'])
            ->whereNotNull('waktu')
            ->get(['waktu'])
            ->mapWithKeys(fn (Jadwal $jadwal) => [
                $jadwal->waktu->format('Y-m') => 'Wisuda '
                    . $monthNames[(int) $jadwal->waktu->format('n')]
                    . ' ' . $jadwal->waktu->format('Y'),
            ])
            ->sortKeysDesc();
        $filters = $request->validate([
            'periode' => ['nullable', 'date_format:Y-m', Rule::in($periods->keys()->all())],
        ]);
        $periode = $filters['periode'] ?? $periods->keys()->first();
        $periodStart = $periode ? \Carbon\Carbon::createFromFormat('Y-m', $periode)->startOfMonth() : null;
        $periodEnd = $periode ? \Carbon\Carbon::createFromFormat('Y-m', $periode)->endOfMonth() : null;
        $lecturers = User::where('role', 'dosen')
            ->orderBy('name')
            ->get()
            ->map(function (User $lecturer) use ($periodStart, $periodEnd) {
                $advisingQuery = TugasAkhir::query();
                if ($periodStart && $periodEnd) {
                    $advisingQuery->whereIn('user_id', Jadwal::select('user_id')
                        ->whereIn('jenis', ['sempro', 'sidang'])
                        ->whereBetween('waktu', [$periodStart, $periodEnd])
                        ->distinct());
                }
                $advisingCount = $advisingQuery->where(function ($query) use ($lecturer) {
                    $query->where('dosen_pembimbing_1', $lecturer->id)
                        ->orWhere('dosen_pembimbing_2', $lecturer->id);
                })->distinct('user_id')->count('user_id');

                return [
                    'dosen' => $lecturer,
                    'mahasiswa_bimbingan' => $advisingCount,
                    'penguji_sempro' => $this->examinerCount($lecturer, 'sempro', $periodStart, $periodEnd),
                    'penguji_sidang' => $this->examinerCount($lecturer, 'sidang', $periodStart, $periodEnd),
                ];
            });

        return view('prodi.rekap-dosen', compact('lecturers', 'periods', 'periode'));
    }

    private function examinerCount(User $lecturer, string $jenis, $periodStart, $periodEnd): int
    {
        $query = Jadwal::where('jenis', $jenis)
            ->where(function ($query) use ($lecturer) {
                $query->whereHas('penguji', fn ($query) => $query->where('users.id', $lecturer->id))
                    ->orWhere('dosen_penguji', $lecturer->name);
            });
        if ($periodStart && $periodEnd) {
            $query->whereBetween('waktu', [$periodStart, $periodEnd]);
        }

        return $query->count();
    }

    private function progressRows(): Collection
    {
        return TugasAkhir::with([
            'mahasiswa',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'jadwals' => fn ($query) => $query->latest(),
        ])->latest()->get()->map(function (TugasAkhir $tugasAkhir) {
            $sempro = $tugasAkhir->jadwals
                ->where('jenis', 'sempro')
                ->sortByDesc('created_at')
                ->first();
            $sidang = $tugasAkhir->jadwals
                ->where('jenis', 'sidang')
                ->sortByDesc('created_at')
                ->first();

            $stage = $this->progressStage($tugasAkhir, $sempro, $sidang);

            return [
                'tugasAkhir' => $tugasAkhir,
                'sempro' => $sempro,
                'sidang' => $sidang,
                'progress' => $this->progressLabel($tugasAkhir, $sempro, $sidang),
                ...$stage,
            ];
        });
    }

    private function reportPeriods(Collection $rows): Collection
    {
        $monthNames = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        return $rows->mapWithKeys(function (array $row) use ($monthNames) {
            $period = $this->periodForRow($row);
            $date = \Carbon\Carbon::createFromFormat('Y-m', $period);

            return [
                $period => 'Wisuda ' . $monthNames[(int) $date->format('n')] . ' ' . $date->format('Y'),
            ];
        })->sortKeysDesc();
    }

    private function periodForRow(array $row): string
    {
        $date = $row['sidang']?->waktu
            ?? $row['sempro']?->waktu
            ?? $row['tugasAkhir']->created_at;

        return $date->format('Y-m');
    }

    private function progressStage(TugasAkhir $tugasAkhir, ?Jadwal $sempro, ?Jadwal $sidang): array
    {
        if ($tugasAkhir->status !== 'Disetujui') {
            return ['tahap_key' => 'pengajuan-judul', 'tahap' => 'Pengajuan Judul', 'persentase' => 15];
        }

        if (
            !$tugasAkhir->dosen_pembimbing_1
            || !$tugasAkhir->dosen_pembimbing_2
            || !$sempro
        ) {
            return ['tahap_key' => 'pembimbing', 'tahap' => 'Pembimbing', 'persentase' => 30];
        }

        if (!$sempro->nilaiTervalidasi() || $sempro->hasil !== 'Lulus') {
            return ['tahap_key' => 'sempro', 'tahap' => 'Sempro', 'persentase' => 50];
        }

        if (!$sidang || !$sidang->nilaiTervalidasi() || $sidang->hasil !== 'Lulus') {
            return ['tahap_key' => 'sidang', 'tahap' => 'Sidang', 'persentase' => 80];
        }

        return ['tahap_key' => 'selesai', 'tahap' => 'Selesai', 'persentase' => 100];
    }

    private function progressLabel(TugasAkhir $tugasAkhir, ?Jadwal $sempro, ?Jadwal $sidang): string
    {
        if ($tugasAkhir->status === 'Menunggu') {
            return 'Menunggu Pemeriksaan Judul';
        }

        if ($tugasAkhir->status === 'Perlu Perbaikan') {
            return 'Menunggu Revisi Mahasiswa';
        }

        if ($tugasAkhir->status === 'Ditolak') {
            return 'Judul Ditolak';
        }

        if (!$tugasAkhir->dosen_pembimbing_1 || !$tugasAkhir->dosen_pembimbing_2) {
            return 'Menunggu Penetapan Pembimbing';
        }

        if (!$sempro) {
            return 'Menunggu Pendaftaran Sempro';
        }

        if ($sempro->status === 'ditolak') {
            return 'Pendaftaran Sempro Ditolak';
        }

        if ($sempro->status === 'menunggu') {
            return 'Menunggu Informasi Jadwal Sempro';
        }

        if ($sempro->status === 'terjadwal') {
            return 'Sempro Terjadwal';
        }

        if ($sempro->status === 'selesai' && $sempro->status_validasi_nilai === 'menunggu_validasi') {
            return 'Menunggu Validasi Nilai Sempro';
        }

        if ($sempro->status === 'selesai' && $sempro->status_validasi_nilai === 'dikembalikan') {
            return 'Nilai Sempro Dikembalikan ke Dosen';
        }

        if ($sempro->status !== 'selesai' || !$sempro->nilaiTervalidasi()) {
            return 'Menunggu Hasil Sempro';
        }

        if ($sempro->hasil === 'Mengulang') {
            return 'Perlu Mengulang Sempro';
        }

        if (!$sidang) {
            return 'Menunggu Pendaftaran Sidang/Komprehensif';
        }

        if ($sidang->status === 'ditolak') {
            return 'Pendaftaran Sidang/Komprehensif Ditolak';
        }

        if ($sidang->status === 'menunggu') {
            return 'Menunggu Informasi Jadwal Sidang/Komprehensif';
        }

        if ($sidang->status === 'terjadwal') {
            return 'Sidang/Komprehensif Terjadwal';
        }

        if ($sidang->status === 'selesai' && $sidang->status_validasi_nilai === 'menunggu_validasi') {
            return 'Menunggu Validasi Nilai Sidang/Komprehensif';
        }

        if ($sidang->status === 'selesai' && $sidang->status_validasi_nilai === 'dikembalikan') {
            return 'Nilai Sidang/Komprehensif Dikembalikan ke Dosen';
        }

        if ($sidang->status !== 'selesai' || !$sidang->nilaiTervalidasi()) {
            return 'Menunggu Hasil Sidang/Komprehensif';
        }

        return $sidang->hasil === 'Lulus' ? 'Selesai' : 'Perlu Mengulang Sidang/Komprehensif';
    }
}
