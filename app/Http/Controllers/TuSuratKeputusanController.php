<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\Document;
use App\Notifications\MahasiswaNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TuSuratKeputusanController extends Controller
{
    public function dashboard()
    {
        $requests = $this->requests();
        $pending = $requests->where('status', 'perlu')->values();

        return view('tu.dashboard', [
            'statistics' => $this->statistics($requests),
            'latestRequests' => $requests->take(6),
            'pendingRequests' => $pending->take(4),
            'documentCount' => Document::count(),
        ]);
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'jenis' => 'nullable|in:semua,pembimbing,penguji',
            'status' => 'nullable|in:semua,perlu,terbit',
            'q' => 'nullable|string|max:255',
        ]);

        $allRequests = $this->requests();
        $requests = $allRequests;

        if (($filters['jenis'] ?? 'semua') !== 'semua') {
            $requests = $requests->where('jenis', $filters['jenis']);
        }

        $statisticsRequests = ($filters['jenis'] ?? 'semua') === 'semua'
            ? $allRequests
            : $allRequests->where('jenis', $filters['jenis']);

        if (($filters['status'] ?? 'semua') !== 'semua') {
            $requests = $requests->where('status', $filters['status']);
        }

        if (! empty($filters['q'])) {
            $search = trim($filters['q']);
            $requests = $requests->filter(fn (array $item) => stripos($item['mahasiswa'], $search) !== false
                || stripos($item['jenis_label'], $search) !== false
                || stripos($item['nomor_sk'] ?? '', $search) !== false
            );
        }

        return view('tu.administrasi-sk', [
            'requests' => $requests->values(),
            'statistics' => $this->statistics($statisticsRequests),
            'filters' => [
                'jenis' => $filters['jenis'] ?? 'semua',
                'status' => $filters['status'] ?? 'semua',
                'q' => $filters['q'] ?? '',
            ],
        ]);
    }

    public function process(string $jenis, int $id)
    {
        $record = $this->findSource($jenis, $id);
        $fields = $this->fields($jenis);

        if ($record->{$fields['file']}) {
            return redirect()->route('tu.administrasi-sk')
                ->withErrors('SK ini sudah diterbitkan.');
        }

        return view('tu.proses-sk', [
            'record' => $record,
            'jenis' => $jenis,
            'jenisLabel' => $fields['label'],
            'requestedAt' => $record->{$fields['requested_at']} ?? $record->updated_at,
        ]);
    }

    public function issue(Request $request, string $jenis, int $id)
    {
        $record = $this->findSource($jenis, $id);
        $fields = $this->fields($jenis);

        if ($record->{$fields['file']}) {
            return redirect()->route('tu.administrasi-sk')
                ->withErrors('SK ini sudah diterbitkan.');
        }

        $validated = $request->validate([
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date',
            'file_sk' => 'required|file|mimes:pdf|max:10240',
        ], [
            'file_sk.mimes' => 'File SK harus berformat PDF.',
            'file_sk.max' => 'Ukuran file SK maksimal 10 MB.',
        ]);

        $file = $request->file('file_sk');
        $filePath = $file->store('surat-keputusan/'.$jenis, 'public');

        if ($filePath === false) {
            throw new \RuntimeException('File SK gagal disimpan.');
        }

        $oldFilePath = $record->{$fields['file']};
        $record->update([
            $fields['number'] => $validated['nomor_sk'],
            $fields['date'] => $validated['tanggal_sk'],
            $fields['file'] => $filePath,
            $fields['requested_at'] => $record->{$fields['requested_at']} ?? $record->updated_at ?? now(),
        ]);

        if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
            if (! Storage::disk('public')->delete($oldFilePath)) {
                Log::warning('Old SK file could not be deleted.', ['path' => $oldFilePath]);
            }
        }

        $record->mahasiswa->notify(new MahasiswaNotification(
            $fields['label'].' telah diterbitkan.',
            route('dokumen')
        ));

        return redirect()->route('tu.administrasi-sk')->with('success', 'SK berhasil diterbitkan.');
    }

    public function download(string $jenis, int $id)
    {
        $record = $this->findSource($jenis, $id);
        $fields = $this->fields($jenis);
        $filePath = $record->{$fields['file']};
        abort_unless($filePath, 404);

        $user = auth()->user();
        $role = strtolower(trim((string) $user->role));
        $allowed = $role === 'tu';

        if ($role === 'mahasiswa') {
            $allowed = (int) $record->user_id === (int) $user->id;
        } elseif ($role === 'dosen') {
            $allowed = $record instanceof TugasAkhir
                ? in_array((int) $user->id, [
                    (int) $record->dosen_pembimbing_1,
                    (int) $record->dosen_pembimbing_2,
                ], true)
                : $record->penguji()->where('users.id', $user->id)->exists()
                    || trim((string) $record->dosen_penguji) === $user->name;
        }

        abort_unless($allowed, 403);
        abort_unless(Storage::disk('public')->exists($filePath), 404);

        return Storage::disk('public')->download($filePath);
    }

    private function requests(): Collection
    {
        $requests = collect();

        TugasAkhir::with('mahasiswa')
            ->where(function ($query) {
                $query->where('status', 'Disetujui')
                    ->orWhereNotNull('sk_pembimbing_file');
            })
            ->get()
            ->each(fn (TugasAkhir $record) => $requests->push($this->row($record, 'pembimbing')));

        Jadwal::with('mahasiswa')
            ->whereIn('jenis', ['sempro', 'sidang'])
            ->where(function ($query) {
                $query->whereIn('status', ['terjadwal', 'selesai'])
                    ->orWhereNotNull('sk_tim_penguji_file');
            })
            ->get()
            ->each(fn (Jadwal $record) => $requests->push($this->row($record, 'penguji')));

        return $requests
            ->sortByDesc(fn (array $item) => $item['requested_at']?->timestamp ?? 0)
            ->values();
    }

    private function row(TugasAkhir|Jadwal $record, string $jenis): array
    {
        $fields = $this->fields($jenis);

        return [
            'id' => $record->id,
            'jenis' => $jenis,
            'jenis_label' => $fields['label'],
            'mahasiswa' => $record->mahasiswa->name,
            'requested_at' => $record->{$fields['requested_at']} ?? $record->updated_at,
            'nomor_sk' => $record->{$fields['number']},
            'file' => $record->{$fields['file']},
            'status' => $record->{$fields['file']} ? 'terbit' : 'perlu',
        ];
    }

    private function statistics(Collection $requests): array
    {
        return [
            'pembimbing_perlu' => $requests->where('jenis', 'pembimbing')->where('status', 'perlu')->count(),
            'penguji_perlu' => $requests->where('jenis', 'penguji')->where('status', 'perlu')->count(),
            'pembimbing_terbit' => $requests->where('jenis', 'pembimbing')->where('status', 'terbit')->count(),
            'penguji_terbit' => $requests->where('jenis', 'penguji')->where('status', 'terbit')->count(),
            'perlu' => $requests->where('status', 'perlu')->count(),
            'terbit' => $requests->where('status', 'terbit')->count(),
        ];
    }

    private function findSource(string $jenis, int $id): TugasAkhir|Jadwal
    {
        if ($jenis === 'pembimbing') {
            return TugasAkhir::with('mahasiswa')
                ->where(function ($query) {
                    $query->where('status', 'Disetujui')
                        ->orWhereNotNull('sk_pembimbing_file');
                })
                ->findOrFail($id);
        }

        if ($jenis === 'penguji') {
            return Jadwal::with('mahasiswa')
                ->whereIn('jenis', ['sempro', 'sidang'])
                ->where(function ($query) {
                    $query->whereIn('status', ['terjadwal', 'selesai'])
                        ->orWhereNotNull('sk_tim_penguji_file');
                })
                ->findOrFail($id);
        }

        abort(404);
    }

    private function fields(string $jenis): array
    {
        if ($jenis === 'pembimbing') {
            return [
                'label' => 'SK Pembimbing',
                'number' => 'sk_pembimbing_nomor',
                'date' => 'sk_pembimbing_tanggal',
                'file' => 'sk_pembimbing_file',
                'requested_at' => 'sk_pembimbing_diminta_at',
            ];
        }

        if ($jenis === 'penguji') {
            return [
                'label' => 'SK Tim Penguji',
                'number' => 'sk_tim_penguji_nomor',
                'date' => 'sk_tim_penguji_tanggal',
                'file' => 'sk_tim_penguji_file',
                'requested_at' => 'sk_tim_penguji_diminta_at',
            ];
        }

        abort(404);
    }
}
