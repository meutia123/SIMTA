<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use App\Notifications\MahasiswaNotification;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class ProdiJadwalController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::with(['mahasiswa', 'tugasAkhir', 'penguji'])
            ->latest()
            ->get();
        $mahasiswa = User::where('role', 'mahasiswa')
            ->whereIn('id', TugasAkhir::select('user_id')->distinct())
            ->orderBy('name')
            ->get();

        return view('prodi.jadwal', compact('jadwal', 'mahasiswa'));
    }

    public function sempro(Request $request)
    {
        return $this->indexByJenis($request, 'sempro');
    }

    public function sidang(Request $request)
    {
        return $this->indexByJenis($request, 'sidang');
    }

    private function indexByJenis(Request $request, string $jenis)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
        ]);
        $query = Jadwal::with(['mahasiswa', 'tugasAkhir', 'penguji'])
            ->where('jenis', $jenis);

        if (!empty($validated['q'])) {
            $search = trim($validated['q']);
            $query->where(function (Builder $query) use ($search) {
                $query->whereHas('mahasiswa', function (Builder $query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nim', 'like', '%' . $search . '%');
                })->orWhereHas('tugasAkhir', function (Builder $query) use ($search) {
                    $query->where('judul', 'like', '%' . $search . '%');
                });
            });
        }

        $jadwal = $query->latest()->get();
        $judul = $jenis === 'sempro' ? 'Kelola Sempro' : 'Kelola Sidang/Komprehensif';
        $description = $jenis === 'sempro'
            ? 'Kelola pendaftaran, jadwal, dan hasil seminar proposal.'
            : 'Kelola pendaftaran, jadwal, dan hasil Sidang/Komprehensif.';
        $dosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('prodi.kelola-ujian', compact('jadwal', 'jenis', 'judul', 'description', 'dosen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'jenis' => 'required|in:bimbingan',
            'waktu' => 'required|date|after:now',
            'lokasi' => 'required|string|max:255',
            'dosen_penguji' => 'required|string|max:255',
        ]);

        $mahasiswa = User::where('role', 'mahasiswa')->find($validated['user_id']);
        if (!$mahasiswa) {
            return back()->withErrors(['user_id' => 'Pilih akun mahasiswa yang valid.'])->withInput();
        }

        $tugasAkhir = TugasAkhir::where('user_id', $mahasiswa->id)->latest()->first();
        if (!$tugasAkhir) {
            return back()->withErrors(['user_id' => 'Mahasiswa harus memiliki pengajuan tugas akhir.'])->withInput();
        }

        $jadwal = Jadwal::create([
            ...$validated,
            'tugas_akhir_id' => $tugasAkhir->id,
            'status' => 'terjadwal',
        ]);
        $mahasiswa->notify(new MahasiswaNotification(
            'Jadwal ' . $jadwal->jenis . ' telah ditetapkan.',
            route('jadwal')
        ));

        return redirect()->route('prodi.jadwal')->with('success', 'Jadwal berhasil dibuat.');
    }

    public function tetapkan(Request $request, Jadwal $jadwal)
    {
        if (in_array($jadwal->status, ['ditolak', 'selesai'], true)) {
            return back()->withErrors('Pendaftaran yang ditolak atau selesai tidak dapat diubah.');
        }

        $validated = $request->validate([
            'waktu' => 'required|date|after:now',
            'lokasi' => 'required|string|max:255',
        ]);
        $examiners = $this->validateExaminers($request);

        $changes = [
            ...$validated,
            'dosen_penguji' => $examiners['label'],
            'status' => 'terjadwal',
            'catatan' => null,
        ];

        if (
            in_array($jadwal->jenis, ['sempro', 'sidang'], true)
            && ! $jadwal->sk_tim_penguji_diminta_at
        ) {
            $changes['sk_tim_penguji_diminta_at'] = now();
        }

        $jadwal->update($changes);
        if ($examiners['ids'] !== null) {
            $jadwal->penguji()->sync($examiners['ids']);
        }
        $jadwal->mahasiswa->notify(new MahasiswaNotification(
            'Jadwal ' . $jadwal->jenis . ' telah ditetapkan atau diperbarui.',
            route('jadwal')
        ));

        return $this->redirectToStage($jadwal, 'Jadwal berhasil ditetapkan.', $request->input('return_to'));
    }

    public function tolak(Request $request, Jadwal $jadwal)
    {
        if ($jadwal->status !== 'menunggu') {
            return back()->withErrors('Hanya pendaftaran yang menunggu yang dapat ditolak.');
        }

        $validated = $request->validate([
            'catatan' => 'nullable|string|max:2000',
        ]);

        $jadwal->update([
            'status' => 'ditolak',
            'catatan' => $validated['catatan'] ?? null,
        ]);
        $jadwal->mahasiswa->notify(new MahasiswaNotification(
            'Pendaftaran ' . $jadwal->jenis . ' ditolak oleh Prodi.',
            route($jadwal->jenis === 'sempro' ? 'tugas-akhir.daftar-sempro' : 'tugas-akhir.daftar-sidang')
        ));

        return $this->redirectToStage($jadwal, 'Pendaftaran ditolak.', $request->input('return_to'));
    }

    public function selesai(Request $request, Jadwal $jadwal)
    {
        if ($jadwal->status !== 'terjadwal' || !$jadwal->waktu || $jadwal->waktu->isFuture()) {
            return back()->withErrors('Jadwal hanya dapat ditandai selesai setelah waktu pelaksanaan.');
        }

        $jadwal->update(['status' => 'selesai']);
        $jadwal->mahasiswa->notify(new MahasiswaNotification(
            'Jadwal ' . $jadwal->jenis . ' telah ditandai selesai.',
            route($jadwal->jenis === 'sempro' ? 'tugas-akhir.daftar-sempro' : ($jadwal->jenis === 'sidang' ? 'tugas-akhir.daftar-sidang' : 'jadwal'))
        ));

        return $this->redirectToStage($jadwal, 'Jadwal ditandai selesai.', $request->input('return_to'));
    }

    private function redirectToStage(Jadwal $jadwal, string $message, ?string $returnTo = null)
    {
        $route = $returnTo === $jadwal->jenis
            ? ($jadwal->jenis === 'sempro' ? 'prodi.sempro' : 'prodi.sidang')
            : 'prodi.jadwal';

        return redirect()->route($route)->with('success', $message);
    }

    private function validateExaminers(Request $request): array
    {
        $validated = $request->validate([
            'dosen_penguji' => 'nullable|string|max:255',
            'dosen_penguji_ids' => 'nullable|array|min:1',
            'dosen_penguji_ids.*' => 'required|distinct|exists:users,id',
        ]);
        $ids = $validated['dosen_penguji_ids'] ?? null;

        if (!$ids) {
            if (empty($validated['dosen_penguji'])) {
                throw ValidationException::withMessages([
                    'dosen_penguji' => 'Pilih tim penguji dari dosen atau masukkan nama tim penguji.',
                ]);
            }

            return ['label' => $validated['dosen_penguji'], 'ids' => null];
        }

        $lecturers = User::where('role', 'dosen')
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        if ($lecturers->count() !== count(array_unique($ids))) {
            throw ValidationException::withMessages([
                'dosen_penguji_ids' => 'Semua anggota tim penguji harus berasal dari akun dosen.',
            ]);
        }

        $names = collect($ids)->map(fn ($id) => $lecturers->get($id)->name);

        return ['label' => $names->implode(', '), 'ids' => $ids];
    }
}
