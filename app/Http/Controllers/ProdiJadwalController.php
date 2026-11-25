<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use App\Notifications\MahasiswaNotification;
use Illuminate\Http\Request;

class ProdiJadwalController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::with(['mahasiswa', 'tugasAkhir'])
            ->latest()
            ->get();
        $mahasiswa = User::where('role', 'mahasiswa')
            ->whereIn('id', TugasAkhir::select('user_id')->distinct())
            ->orderBy('name')
            ->get();

        return view('prodi.jadwal', compact('jadwal', 'mahasiswa'));
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
            'dosen_penguji' => 'required|string|max:255',
        ]);

        $jadwal->update([
            ...$validated,
            'status' => 'terjadwal',
            'catatan' => null,
        ]);
        $jadwal->mahasiswa->notify(new MahasiswaNotification(
            'Jadwal ' . $jadwal->jenis . ' telah ditetapkan atau diperbarui.',
            route('jadwal')
        ));

        return redirect()->route('prodi.jadwal')->with('success', 'Jadwal berhasil ditetapkan.');
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

        return redirect()->route('prodi.jadwal')->with('success', 'Pendaftaran ditolak.');
    }

    public function selesai(Jadwal $jadwal)
    {
        if ($jadwal->status !== 'terjadwal' || !$jadwal->waktu || $jadwal->waktu->isFuture()) {
            return back()->withErrors('Jadwal hanya dapat ditandai selesai setelah waktu pelaksanaan.');
        }

        $jadwal->update(['status' => 'selesai']);
        $jadwal->mahasiswa->notify(new MahasiswaNotification(
            'Jadwal ' . $jadwal->jenis . ' telah ditandai selesai.',
            route($jadwal->jenis === 'sempro' ? 'tugas-akhir.daftar-sempro' : ($jadwal->jenis === 'sidang' ? 'tugas-akhir.daftar-sidang' : 'jadwal'))
        ));

        return redirect()->route('prodi.jadwal')->with('success', 'Jadwal ditandai selesai.');
    }
}
