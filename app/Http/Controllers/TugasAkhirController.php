<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TugasAkhirController extends Controller
{
    public function index()
    {
        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())
            ->with(['calonDosen1', 'calonDosen2', 'dosenPembimbing1', 'dosenPembimbing2', 'jadwals'])
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
            ->with(['calonDosen1', 'calonDosen2', 'dosenPembimbing1', 'dosenPembimbing2'])
            ->latest()
            ->first();

        $sempro = Jadwal::where('user_id', Auth::id())
            ->where('jenis', 'sempro')
            ->with('tugasAkhir')
            ->latest()
            ->get();
        $adaPendaftaranAktif = $sempro->contains(fn ($item) => in_array($item->status, ['menunggu', 'terjadwal', 'selesai'], true));

        return view('mahasiswa.daftar-sempro', compact('tugasAkhir', 'sempro', 'adaPendaftaranAktif'));
    }

    public function daftarSidang()
    {
        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())
            ->with(['calonDosen1', 'calonDosen2', 'dosenPembimbing1', 'dosenPembimbing2'])
            ->latest()
            ->first();

        $sidang = Jadwal::where('user_id', Auth::id())
            ->where('jenis', 'sidang')
            ->with('tugasAkhir')
            ->latest()
            ->get();
        $sempro = Jadwal::where('user_id', Auth::id())
            ->where('jenis', 'sempro')
            ->latest()
            ->get();
        $adaPendaftaranAktif = $sidang->contains(fn ($item) => in_array($item->status, ['menunggu', 'terjadwal', 'selesai'], true));

        return view('mahasiswa.daftar-sidang', compact('tugasAkhir', 'sidang', 'sempro', 'adaPendaftaranAktif'));
    }

    public function jadwal()
    {
        $jadwal = Jadwal::where('user_id', Auth::id())
            ->with('tugasAkhir')
            ->orderByRaw("CASE WHEN waktu IS NULL THEN 1 ELSE 0 END")
            ->orderBy('waktu')
            ->latest()
            ->get();

        return view('mahasiswa.pages.jadwal', compact('jadwal'));
    }

    public function ajukanSempro()
    {
        return $this->ajukanSeminar('sempro');
    }

    public function ajukanSidang()
    {
        return $this->ajukanSeminar('sidang');
    }

    private function ajukanSeminar(string $jenis)
    {
        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())->latest()->first();
        $route = $jenis === 'sempro' ? 'tugas-akhir.daftar-sempro' : 'tugas-akhir.daftar-sidang';

        if (!$tugasAkhir) {
            return redirect()->route($route)->withErrors('Ajukan judul tugas akhir terlebih dahulu.');
        }

        if ($tugasAkhir->status !== 'Disetujui') {
            return redirect()->route($route)->withErrors('Pengajuan judul harus disetujui Prodi terlebih dahulu.');
        }

        if ($jenis === 'sidang') {
            $semproSelesai = Jadwal::where('user_id', Auth::id())
                ->where('jenis', 'sempro')
                ->where('status', 'selesai')
                ->exists();

            if (!$semproSelesai) {
                return redirect()->route($route)->withErrors('Seminar Proposal harus ditandai selesai sebelum mendaftar Sidang.');
            }
        }

        $sudahTerdaftar = Jadwal::where('user_id', Auth::id())
            ->where('jenis', $jenis)
            ->whereIn('status', ['menunggu', 'terjadwal', 'selesai'])
            ->exists();

        if ($sudahTerdaftar) {
            return redirect()->route($route)->withErrors('Pendaftaran aktif untuk tahap ini sudah ada.');
        }

        Jadwal::create([
            'user_id' => Auth::id(),
            'tugas_akhir_id' => $tugasAkhir->id,
            'jenis' => $jenis,
            'status' => 'menunggu',
        ]);

        return redirect()->route($route)->with('success', 'Pendaftaran berhasil dikirim ke Prodi.');
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
            'nama_dokumen_lain' => 'required_if:nama_dokumen,Lainnya|nullable|string|max:255',
            'jenis_dokumen' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $tugasAkhir = TugasAkhir::where('user_id', Auth::id())->latest()->first();

        $file = $request->file('file');
        $originalFileName = basename(str_replace('\\', '/', $file->getClientOriginalName()));
        $fileName = Str::uuid() . '_' . $originalFileName;
        $filePath = $file->storeAs('documents/' . Auth::id(), $fileName, 'public');

        if ($filePath === false) {
            throw new \RuntimeException('Dokumen gagal disimpan.');
        }

        Document::create([
            'user_id' => Auth::id(),
            'tugas_akhir_id' => $tugasAkhir?->id,
            'nama_dokumen' => $validated['nama_dokumen'] === 'Lainnya'
                ? $validated['nama_dokumen_lain']
                : $validated['nama_dokumen'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'file_path' => $filePath,
            'file_name' => $originalFileName,
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