<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $mahasiswaTerbaru = $this->adviseesFor($dosen)
            ->with(['mahasiswa', 'dosenPembimbing1', 'dosenPembimbing2'])
            ->latest()
            ->take(5)
            ->get();
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
            'mahasiswaSaya' => $this->adviseesFor($dosen)->distinct('user_id')->count('user_id'),
            'pengujiSempro' => $this->examinerSchedulesFor($dosen)->where('jenis', 'sempro')->count(),
            'pengujiSidang' => $this->examinerSchedulesFor($dosen)->where('jenis', 'sidang')->count(),
            'jadwalHariIni' => $jadwalHariIni,
            'jadwalMendatang' => $jadwalMendatang,
            'mahasiswaTerbaru' => $mahasiswaTerbaru,
            'suratKeputusan' => $suratPembimbing->concat($suratPenguji),
        ]);
    }

    public function jadwal(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:255',
        ]);
        $query = $this->schedulesFor(Auth::user())
            ->with(['mahasiswa', 'tugasAkhir', 'penguji', 'dosenPenilai']);

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

        return view('dosen.jadwal', [
            'jadwal' => $jadwal,
            'search' => $filters['q'] ?? '',
        ]);
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
}
