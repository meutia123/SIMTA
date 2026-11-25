<?php

namespace App\Http\Controllers;

use App\Models\TugasAkhir;
use App\Models\User;
use App\Notifications\MahasiswaNotification;
use Illuminate\Http\Request;

class ProdiTugasAkhirController extends Controller
{
    public function index()
    {
        $pengajuan = TugasAkhir::with([
            'mahasiswa',
            'calonDosen1',
            'calonDosen2',
            'dosenPembimbing1',
            'dosenPembimbing2',
        ])->latest()->get();
        $dosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('prodi.pengajuan-tugas-akhir', compact('pengajuan', 'dosen'));
    }

    public function update(Request $request, TugasAkhir $tugasAkhir)
    {
        $validated = $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Ditolak',
            'dosen_pembimbing_1' => 'required_if:status,Disetujui|nullable|exists:users,id',
            'dosen_pembimbing_2' => 'required_if:status,Disetujui|nullable|different:dosen_pembimbing_1|exists:users,id',
            'catatan_prodi' => 'nullable|string|max:2000',
        ]);

        if ($validated['status'] === 'Disetujui') {
            foreach (['dosen_pembimbing_1', 'dosen_pembimbing_2'] as $field) {
                if (!User::where('id', $validated[$field])->where('role', 'dosen')->exists()) {
                    return back()->withErrors([$field => 'Pembimbing harus dipilih dari akun dosen.'])->withInput();
                }
            }
        }

        $tugasAkhir->update([
            'status' => $validated['status'],
            'dosen_pembimbing_1' => $validated['status'] === 'Disetujui' ? $validated['dosen_pembimbing_1'] : null,
            'dosen_pembimbing_2' => $validated['status'] === 'Disetujui' ? $validated['dosen_pembimbing_2'] : null,
            'catatan_prodi' => $validated['catatan_prodi'] ?? null,
        ]);

        $tugasAkhir->mahasiswa->notify(new MahasiswaNotification(
            'Status pengajuan judul diperbarui menjadi ' . $tugasAkhir->status . '.',
            route('tugas-akhir')
        ));

        return redirect()->route('prodi.pengajuan-tugas-akhir')->with('success', 'Pengajuan berhasil diperbarui.');
    }
}
