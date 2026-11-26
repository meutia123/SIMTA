<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenNilaiController extends Controller
{
    public function edit(Jadwal $jadwal)
    {
        $this->ensureCanSubmit($jadwal);

        return view('dosen.nilai', compact('jadwal'));
    }

    public function store(Request $request, Jadwal $jadwal)
    {
        $this->ensureCanSubmit($jadwal);
        $validated = $request->validate([
            'nilai_presentasi' => 'required|numeric|min:0|max:100',
            'nilai_naskah' => 'required|numeric|min:0|max:100',
            'nilai_penguasaan' => 'required|numeric|min:0|max:100',
            'hasil' => 'required|in:Lulus,Mengulang',
        ]);
        $nilaiAkhir = round((
            (float) $validated['nilai_presentasi']
            + (float) $validated['nilai_naskah']
            + (float) $validated['nilai_penguasaan']
        ) / 3, 2);

        $jadwal->update([
            ...$validated,
            'nilai' => $nilaiAkhir,
            'dosen_penilai_id' => Auth::id(),
            'status_validasi_nilai' => 'menunggu_validasi',
            'catatan_validasi_nilai' => null,
        ]);

        return redirect()->route('dosen.jadwal')->with(
            'success',
            'Nilai berhasil dikirim dan menunggu validasi Prodi.'
        );
    }

    private function ensureCanSubmit(Jadwal $jadwal): void
    {
        abort_unless(
            in_array($jadwal->jenis, ['sempro', 'sidang'], true)
            && $jadwal->status === 'selesai',
            404
        );

        $dosen = Auth::user();
        $isPembimbing = $jadwal->tugasAkhir()
            ->where(function ($query) use ($dosen) {
                $query->where('dosen_pembimbing_1', $dosen->id)
                    ->orWhere('dosen_pembimbing_2', $dosen->id);
            })
            ->exists();
        $isPenguji = $jadwal->penguji()->where('users.id', $dosen->id)->exists()
            || $jadwal->dosen_penguji === $dosen->name;

        abort_unless($isPembimbing || $isPenguji, 403);
        abort_if($jadwal->nilaiTervalidasi(), 403);
        abort_if($jadwal->status_validasi_nilai === 'menunggu_validasi', 403);
        abort_if(
            $jadwal->status_validasi_nilai === 'dikembalikan'
            && (int) $jadwal->dosen_penilai_id !== (int) $dosen->id,
            403
        );
    }
}
