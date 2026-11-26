<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\TugasAkhir;
use App\Models\User;
use App\Notifications\MahasiswaNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TuDocumentController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:255',
            'status' => 'nullable|in:semua,diupload,diverifikasi,ditolak,draft',
        ]);
        $query = Document::with(['user', 'tugasAkhir'])->latest();

        if (($filters['status'] ?? 'semua') !== 'semua') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function ($query) use ($search) {
                $query->where('nama_dokumen', 'like', '%' . $search . '%')
                    ->orWhere('jenis_dokumen', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nim', 'like', '%' . $search . '%');
                    });
            });
        }

        return view('tu.dokumen', [
            'documents' => $query->get(),
            'students' => User::where('role', 'mahasiswa')->orderBy('name')->get(),
            'filters' => [
                'q' => $filters['q'] ?? '',
                'status' => $filters['status'] ?? 'semua',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_dokumen' => 'required|string|max:255',
            'jenis_dokumen' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if (in_array($validated['nama_dokumen'], ['SK Pembimbing', 'SK Tim Penguji'], true)) {
            return back()->withErrors([
                'nama_dokumen' => 'SK diterbitkan melalui menu Administrasi SK.',
            ])->withInput();
        }

        $student = User::where('role', 'mahasiswa')->find($validated['user_id']);

        if (!$student) {
            return back()->withErrors(['user_id' => 'Pilih akun mahasiswa yang valid.'])->withInput();
        }

        $tugasAkhir = TugasAkhir::where('user_id', $student->id)->latest()->first();
        $file = $request->file('file');
        $filePath = $file->store('documents/tu/' . $student->id, 'public');

        if ($filePath === false) {
            throw new \RuntimeException('Dokumen gagal disimpan.');
        }

        $document = Document::create([
            'user_id' => $student->id,
            'uploaded_by' => Auth::id(),
            'tugas_akhir_id' => $tugasAkhir?->id,
            'nama_dokumen' => $validated['nama_dokumen'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'file_path' => $filePath,
            'file_name' => basename(str_replace('\\', '/', $file->getClientOriginalName())),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'diverifikasi',
        ]);

        $student->notify(new MahasiswaNotification(
            'TU mengunggah dokumen "' . $document->nama_dokumen . '".',
            route('dokumen')
        ));

        return redirect()->route('tu.dokumen')->with('success', 'Dokumen berhasil diunggah untuk mahasiswa.');
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'jenis_dokumen' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if (in_array($validated['nama_dokumen'], ['SK Pembimbing', 'SK Tim Penguji'], true)) {
            return back()->withErrors([
                'nama_dokumen' => 'SK diterbitkan melalui menu Administrasi SK.',
            ])->withInput();
        }

        $oldFilePath = $document->file_path;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('documents/tu/' . $document->user_id, 'public');

            if ($filePath === false) {
                throw new \RuntimeException('Dokumen pengganti gagal disimpan.');
            }

            $validated['file_path'] = $filePath;
            $validated['file_name'] = basename(str_replace('\\', '/', $file->getClientOriginalName()));
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        $validated['uploaded_by'] = Auth::id();
        $validated['status'] = 'diverifikasi';
        $validated['catatan'] = null;
        $document->update($validated);

        if (
            $request->hasFile('file')
            && Storage::disk('public')->exists($oldFilePath)
            && !Storage::disk('public')->delete($oldFilePath)
        ) {
            Log::warning('Old student document could not be deleted after replacement.', ['path' => $oldFilePath]);
        }

        $document->user->notify(new MahasiswaNotification(
            'Dokumen "' . $document->nama_dokumen . '" telah diperbarui oleh TU.',
            route('dokumen')
        ));

        return redirect()->route('tu.dokumen')->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document)
    {
        if (
            Storage::disk('public')->exists($document->file_path)
            && !Storage::disk('public')->delete($document->file_path)
        ) {
            throw new \RuntimeException('File dokumen gagal dihapus.');
        }

        $document->delete();
        $document->user->notify(new MahasiswaNotification(
            'Dokumen "' . $document->nama_dokumen . '" telah dihapus oleh TU.',
            route('dokumen')
        ));

        return redirect()->route('tu.dokumen')->with('success', 'Dokumen berhasil dihapus.');
    }

    public function download(Document $document)
    {
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
