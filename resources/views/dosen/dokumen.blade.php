@extends('layouts.dosen')

@section('title', 'SK & Dokumen - SIMTA')
@section('page-label', 'SK & Dokumen')
@section('page-description', 'Dokumen mahasiswa bimbingan dan ujian Anda')

@section('content')
<div class="space-y-5">
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h1 class="text-lg font-bold">Surat Keputusan Terkait</h1>
        <p class="mt-1 text-sm text-gray-500">Unduh SK yang diterbitkan TU dan berkaitan dengan penugasan Anda.</p>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[600px] text-left text-sm">
                <thead class="bg-[#f5f3fb] text-xs text-gray-700">
                    <tr><th class="p-3">No</th><th class="p-3">Jenis Dokumen</th><th class="p-3">Mahasiswa</th><th class="p-3">Nomor SK</th><th class="p-3">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($suratKeputusan as $surat)
                        <tr>
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3 font-semibold">{{ $surat['label'] }}</td>
                            <td class="p-3">{{ $surat['mahasiswa'] }}</td>
                            <td class="p-3">{{ $surat['nomor'] ?: '-' }}</td>
                            <td class="p-3">
                                <a href="{{ route('surat-keputusan.download', ['jenis' => $surat['jenis'], 'id' => $surat['id']]) }}"
                                   class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold hover:bg-gray-50">Unduh SK</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-7 text-center text-gray-500">Belum ada SK yang tersedia untuk penugasan Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-bold">Dokumen Mahasiswa</h2>
        <p class="mt-1 text-sm text-gray-500">Dokumen hanya ditampilkan untuk mahasiswa yang menjadi bimbingan atau penugasan ujian Anda.</p>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-[#f5f3fb] text-xs text-gray-700">
                    <tr><th class="p-3">No</th><th class="p-3">Mahasiswa</th><th class="p-3">Dokumen</th><th class="p-3">Tanggal Unggah</th><th class="p-3">Status</th><th class="p-3">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($documents as $document)
                        <tr>
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">{{ $document->user->name }}</td>
                            <td class="p-3">
                                <p class="font-semibold">{{ $document->nama_dokumen }}</p>
                                <p class="text-xs text-gray-500">{{ $document->file_name }}</p>
                            </td>
                            <td class="p-3">{{ $document->created_at->format('d M Y') }}</td>
                            <td class="p-3">{{ ucfirst($document->status) }}</td>
                            <td class="p-3">
                                <a href="{{ route('dosen.dokumen.download', $document) }}" class="font-semibold text-[#841719] hover:underline">Unduh</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-7 text-center text-gray-500">Belum ada dokumen dari mahasiswa terkait.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
