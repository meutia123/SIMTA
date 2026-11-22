<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TugasAkhirController extends Controller
{
    public function index()
    {
        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())
            ->latest()
            ->first();

        $dosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('mahasiswa.tugas-akhir', compact('tugasAkhir', 'dosen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'topik' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'calon_dosen_1' => 'required|exists:users,id',
            'calon_dosen_2' => 'required|different:calon_dosen_1|exists:users,id',
            'calon_dosen_3' => 'required|different:calon_dosen_1|different:calon_dosen_2|exists:users,id',
        ]);

        foreach (['calon_dosen_1', 'calon_dosen_2', 'calon_dosen_3'] as $field) {
            $isDosen = User::where('id', $validated[$field])
                ->where('role', 'dosen')
                ->exists();

            if (!$isDosen) {
                return back()->withErrors([
                    $field => 'Pilihan harus berasal dari akun dosen.'
                ])->withInput();
            }
        }

        TugasAkhir::create([
            'user_id' => Auth::id(),
            'judul' => $validated['judul'],
            'topik' => $validated['topik'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'calon_dosen_1' => $validated['calon_dosen_1'],
            'calon_dosen_2' => $validated['calon_dosen_2'],
            'calon_dosen_3' => $validated['calon_dosen_3'],
            'status' => 'Menunggu'
        ]);

        return redirect()
            ->route('tugas-akhir')
            ->with('success', 'Pengajuan judul berhasil dikirim.');
    }
}