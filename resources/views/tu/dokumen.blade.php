@extends('layouts.tu')

@section('title', 'Dokumen Tugas Akhir - SIMTA')
@section('page-label', 'Dokumen Tugas Akhir')
@section('page-description', 'Unggah dan perbarui dokumen mahasiswa')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h1 class="text-lg font-bold">Unggah Dokumen untuk Mahasiswa</h1>
        <p class="mt-1 text-sm text-gray-500">Dokumen yang diunggah TU langsung tersedia pada halaman Dokumen mahasiswa.</p>
        <form action="{{ route('tu.dokumen.store') }}" method="POST" enctype="multipart/form-data" class="mt-5 grid gap-4 md:grid-cols-2">
            @csrf
            <label class="text-sm font-semibold">
                Mahasiswa
                <select name="user_id" required class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 font-normal">
                    <option value="">Pilih mahasiswa</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}{{ $student->nim ? ' (' . $student->nim . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-semibold">
                Nama Dokumen
                <input type="text" name="nama_dokumen" required maxlength="255" value="{{ old('nama_dokumen') }}"
                       placeholder="Contoh: Lembar Persetujuan"
                       class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 font-normal">
            </label>
            <label class="text-sm font-semibold">
                Jenis Dokumen
                <input type="text" name="jenis_dokumen" required maxlength="255" value="{{ old('jenis_dokumen') }}"
                       placeholder="Contoh: Administrasi Tugas Akhir"
                       class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 font-normal">
            </label>
            <label class="text-sm font-semibold">
                File (PDF, DOC, DOCX; maksimal 10 MB)
                <input type="file" name="file" required accept=".pdf,.doc,.docx"
                       class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 font-normal">
            </label>
            <div class="md:col-span-2">
                <button class="rounded-lg bg-[#841719] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#681315]">
                    Unggah Dokumen
                </button>
            </div>
        </form>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-bold">Dokumen Tugas Akhir</h2>
                <p class="mt-1 text-xs text-gray-500">TU dapat memperbarui informasi atau mengganti file.</p>
            </div>
            <form action="{{ route('tu.dokumen') }}" method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Nama, NIM, dokumen"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <select name="status" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                    @foreach(['semua' => 'Semua Status', 'diupload' => 'Diunggah', 'diverifikasi' => 'Diverifikasi', 'ditolak' => 'Ditolak', 'draft' => 'Draft'] as $value => $label)
                        <option value="{{ $value }}" {{ $filters['status'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-[#596329] px-4 py-2 text-sm font-semibold text-white">Cari</button>
            </form>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">Mahasiswa</th>
                        <th class="p-3">Dokumen</th>
                        <th class="p-3">Jenis</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">File</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($documents as $document)
                        <tr class="align-top">
                            <td class="p-3">
                                <p class="font-semibold">{{ $document->user->name }}</p>
                                <p class="mt-1 text-gray-500">{{ $document->user->nim ?: '-' }}</p>
                            </td>
                            <td class="p-3 font-semibold">{{ $document->nama_dokumen }}</td>
                            <td class="p-3">{{ $document->jenis_dokumen }}</td>
                            <td class="p-3">{{ ucfirst($document->status) }}</td>
                            <td class="p-3">
                                <a href="{{ route('tu.dokumen.download', $document) }}" class="font-semibold text-[#841719] hover:underline">
                                    Unduh {{ $document->file_name }}
                                </a>
                            </td>
                            <td class="p-3">
                                <details>
                                    <summary class="cursor-pointer font-semibold text-[#596329]">Perbarui</summary>
                                    <form action="{{ route('tu.dokumen.update', $document) }}" method="POST" enctype="multipart/form-data" class="mt-3 min-w-56 space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <label class="block font-semibold">
                                            Nama
                                            <input type="text" name="nama_dokumen" required maxlength="255" value="{{ $document->nama_dokumen }}"
                                                   class="mt-1 w-full rounded border border-gray-300 px-2 py-1.5 font-normal">
                                        </label>
                                        <label class="block font-semibold">
                                            Jenis
                                            <input type="text" name="jenis_dokumen" required maxlength="255" value="{{ $document->jenis_dokumen }}"
                                                   class="mt-1 w-full rounded border border-gray-300 px-2 py-1.5 font-normal">
                                        </label>
                                        <label class="block font-semibold">
                                            Ganti File (opsional)
                                            <input type="file" name="file" accept=".pdf,.doc,.docx"
                                                   class="mt-1 block w-full rounded border border-gray-300 px-2 py-1.5 font-normal">
                                        </label>
                                        <button class="w-full rounded bg-[#841719] px-3 py-1.5 font-semibold text-white">Simpan Perubahan</button>
                                    </form>
                                    <form action="{{ route('tu.dokumen.destroy', $document) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Hapus dokumen ini?')" class="font-semibold text-red-700 hover:underline">Hapus Dokumen</button>
                                    </form>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-gray-500">Belum ada dokumen tugas akhir yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
