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
        $existing = TugasAkhir::where('user_id', Auth::id())->latest()->first();
        if ($existing && $existing->status !== 'Perlu Perbaikan') {
            return back()->withErrors(
                'Pengajuan hanya dapat dikirim ulang setelah Prodi meminta perbaikan.'
            );
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'topik' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'calon_dosen_1' => 'required|exists:users,id',
            'calon_dosen_2' => 'required|different:calon_dosen_1|different:calon_dosen_3|exists:users,id',
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

        $data = [
            'judul' => $validated['judul'],
            'topik' => $validated['topik'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'calon_dosen_1' => $validated['calon_dosen_1'],
            'calon_dosen_2' => $validated['calon_dosen_2'],
            'calon_dosen_3' => $validated['calon_dosen_3'],
            'dosen_pembimbing_1' => null,
            'dosen_pembimbing_2' => null,
            'status' => 'Menunggu',
            'catatan_prodi' => null,
        ];

        if ($existing) {
            $existing->update($data);

            return redirect()
                ->route('tugas-akhir')
                ->with('success', 'Perbaikan pengajuan berhasil dikirim ulang.');
        }

        TugasAkhir::create([
            ...$data,
            'user_id' => Auth::id(),
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
        $latestSempro = $sempro->first();
        $adaPendaftaranAktif = $latestSempro && $this->examStillActive($latestSempro);

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
        $latestSempro = $sempro->first();
        $latestSidang = $sidang->first();
        $semproLulus = $latestSempro?->status === 'selesai'
            && $latestSempro->nilaiTervalidasi()
            && $latestSempro->hasil === 'Lulus';
        $adaPendaftaranAktif = $latestSidang && $this->examStillActive($latestSidang);

        return view('mahasiswa.daftar-sidang', compact('tugasAkhir', 'sidang', 'sempro', 'semproLulus', 'adaPendaftaranAktif'));
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

        if (!$tugasAkhir->dosen_pembimbing_1 || !$tugasAkhir->dosen_pembimbing_2) {
            return redirect()->route($route)->withErrors('Prodi harus menetapkan dua dosen pembimbing terlebih dahulu.');
        }

        if ($jenis === 'sidang') {
            $latestSempro = Jadwal::where('user_id', Auth::id())
                ->where('jenis', 'sempro')
                ->latest()
                ->first();

            if (
                !$latestSempro
                || $latestSempro->status !== 'selesai'
                || !$latestSempro->nilaiTervalidasi()
                || $latestSempro->hasil !== 'Lulus'
            ) {
                return redirect()->route($route)->withErrors('Sempro terbaru harus selesai dan nilainya divalidasi Prodi dengan hasil Lulus sebelum mendaftar Sidang/Komprehensif.');
            }
        }

        $latestExam = Jadwal::where('user_id', Auth::id())
            ->where('jenis', $jenis)
            ->latest()
            ->first();

        $canRepeat = $latestExam
            && $latestExam->status === 'selesai'
            && $latestExam->nilaiTervalidasi()
            && $latestExam->hasil === 'Mengulang';

        if ($latestExam && $latestExam->status !== 'ditolak' && !$canRepeat) {
            return redirect()->route($route)->withErrors('Pendaftaran aktif atau hasil Lulus untuk tahap ini sudah tercatat.');
        }

        Jadwal::create([
            'user_id' => Auth::id(),
            'tugas_akhir_id' => $tugasAkhir->id,
            'jenis' => $jenis,
            'status' => 'menunggu',
        ]);

        return redirect()->route($route)->with('success', 'Pendaftaran berhasil dikirim ke Prodi.');
    }

    private function examStillActive(Jadwal $jadwal): bool
    {
        if (in_array($jadwal->status, ['menunggu', 'terjadwal'], true)) {
            return true;
        }

        return $jadwal->status === 'selesai'
            && (!$jadwal->nilaiTervalidasi() || $jadwal->hasil !== 'Mengulang');
    }

    public function dokumen()
    {
        $tugasAkhir = TugasAkhir::with('jadwals')->where('user_id', Auth::id())
            ->latest()
            ->first();

        $documents = Document::where('user_id', Auth::id())
            ->with('tugasAkhir')
            ->latest()
            ->get();
        $suratKeputusan = collect();

        if ($tugasAkhir?->sk_pembimbing_file) {
            $suratKeputusan->push([
                'nama' => 'SK Pembimbing',
                'jenis' => 'pembimbing',
                'id' => $tugasAkhir->id,
                'nomor' => $tugasAkhir->sk_pembimbing_nomor,
                'tanggal' => $tugasAkhir->sk_pembimbing_tanggal,
            ]);
        }

        foreach ($tugasAkhir?->jadwals ?? [] as $jadwal) {
            if ($jadwal->sk_tim_penguji_file) {
                $suratKeputusan->push([
                    'nama' => 'SK Tim Penguji',
                    'jenis' => 'penguji',
                    'id' => $jadwal->id,
                    'nomor' => $jadwal->sk_tim_penguji_nomor,
                    'tanggal' => $jadwal->sk_tim_penguji_tanggal,
                    'tahap' => ucfirst($jadwal->jenis),
                ]);
            }
        }

        $dokumenWajib = [
            ['nama' => 'SK Pembimbing', 'jenis' => 'Surat Keputusan', 'wajib' => true],
            ['nama' => 'Proposal Tugas Akhir', 'jenis' => 'Proposal', 'wajib' => true],
            ['nama' => 'SK Tim Penguji', 'jenis' => 'Surat Keputusan', 'wajib' => true],
            ['nama' => 'Berita Acara', 'jenis' => 'Berita Acara', 'wajib' => true],
            ['nama' => 'Lembar Persetujuan', 'jenis' => 'Lembar Persetujuan', 'wajib' => true],
        ];

        return view('mahasiswa.pages.dokumen', compact('tugasAkhir', 'documents', 'dokumenWajib', 'suratKeputusan'));
    }

    public function uploadDokumen(Request $request)
    {
        $validated = $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'nama_dokumen_lain' => 'required_if:nama_dokumen,Lainnya|nullable|string|max:255',
            'jenis_dokumen' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $namaDokumen = $validated['nama_dokumen'] === 'Lainnya'
            ? $validated['nama_dokumen_lain']
            : $validated['nama_dokumen'];

        if (in_array($namaDokumen, ['SK Pembimbing', 'SK Tim Penguji'], true)) {
            return back()->withErrors([
                'nama_dokumen' => 'Surat Keputusan diterbitkan oleh TU dan dapat diunduh setelah tersedia.',
            ])->withInput();
        }

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
            'uploaded_by' => Auth::id(),
            'tugas_akhir_id' => $tugasAkhir?->id,
            'nama_dokumen' => $namaDokumen,
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
        if ((int) $document->user_id !== (int) Auth::id()) {
            abort(403);
        }

        if ($document->uploaded_by && (int) $document->uploaded_by !== (int) Auth::id()) {
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
        if ((int) $document->user_id !== (int) Auth::id()) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}