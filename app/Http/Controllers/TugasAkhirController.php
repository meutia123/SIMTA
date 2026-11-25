<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TugasAkhir;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

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
        ]);

        foreach (['calon_dosen_1', 'calon_dosen_2'] as $field) {
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
            'calon_dosen_3' => null,
            'status' => 'Menunggu'
        ]);

        return redirect()
            ->route('tugas-akhir')
            ->with('success', 'Pengajuan judul berhasil dikirim.');
    }

    public function ajukanJudul()
    {
        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())
            ->latest()
            ->first();

        $dosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('mahasiswa.ajukan-judul', compact('tugasAkhir', 'dosen'));
    }

    public function daftarSempro()
    {
        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())
            ->latest()
            ->first();

        $sempro = TugasAkhir::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('mahasiswa.daftar-sempro', compact('tugasAkhir', 'sempro'));
    }

    public function daftarSidang()
    {
        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())
            ->latest()
            ->first();

        $sidang = TugasAkhir::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('mahasiswa.daftar-sidang', compact('tugasAkhir', 'sidang'));
    }

    public function dokumen()
    {
        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())
            ->latest()
            ->first();

        $documents = Document::where('user_id', Auth::id())
            ->with('tugasAkhir')
            ->latest()
            ->get();

        $dokumenWajib = [
            ['nama' => 'SK Pembimbing', 'jenis' => 'Surat Keputusan', 'wajib' => true],
            ['nama' => 'Proposal Tugas Akhir', 'jenis' => 'Proposal', 'wajib' => true],
            ['nama' => 'SK Tim Penguji', 'jenis' => 'Surat Keputusan', 'wajib' => true],
            ['nama' => 'Berita Acara', 'jenis' => 'Berita Acara', 'wajib' => true],
            ['nama' => 'Lembar Persetujuan', 'jenis' => 'Lembar Persetujuan', 'wajib' => true],
        ];

        return view('mahasiswa.pages.dokumen', compact('tugasAkhir', 'documents', 'dokumenWajib'));
    }

    public function uploadDokumen(Request $request)
    {
        $validated = $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'jenis_dokumen' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())->latest()->first();

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents/' . Auth::id(), $fileName, 'public');

        Document::create([
            'user_id' => Auth::id(),
            'tugas_akhir_id' => $tugasAkhir?->id,
            'nama_dokumen' => $validated['nama_dokumen'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'diupload',
        ]);

        return redirect()
            ->route('dokumen')
            ->with('success', 'Dokumen berhasil diunggah.');
    }

    public function hapusDokumen(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('dokumen')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    public function downloadDokumen(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}