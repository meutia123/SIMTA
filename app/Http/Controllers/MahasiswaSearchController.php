<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Jadwal;
use App\Models\Pengumuman;
use App\Models\TugasAkhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaSearchController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|max:100',
        ]);
        $query = trim($validated['q']);
        $like = '%' . $query . '%';
        $userId = Auth::id();

        $tugasAkhir = TugasAkhir::where('user_id', $userId)
            ->where(function ($builder) use ($like) {
                $builder->where('judul', 'like', $like)
                    ->orWhere('topik', 'like', $like)
                    ->orWhere('deskripsi', 'like', $like);
            })->latest()->get();

        $documents = Document::where('user_id', $userId)
            ->where(function ($builder) use ($like) {
                $builder->where('nama_dokumen', 'like', $like)
                    ->orWhere('jenis_dokumen', 'like', $like);
            })->latest()->get();

        $jadwal = Jadwal::where('user_id', $userId)
            ->where(function ($builder) use ($like) {
                $builder->where('jenis', 'like', $like)
                    ->orWhere('lokasi', 'like', $like)
                    ->orWhere('dosen_penguji', 'like', $like);
            })->latest()->get();

        $pengumuman = Pengumuman::where('judul', 'like', $like)
            ->orWhere('isi', 'like', $like)
            ->latest()
            ->get();

        return view('mahasiswa.pages.search', compact('query', 'tugasAkhir', 'documents', 'jadwal', 'pengumuman'));
    }
}
