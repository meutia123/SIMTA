@extends('layouts.prodi')

@section('title', 'Periksa Judul - SIMTA')
@section('page-label', 'Periksa Judul')
@section('page-description', 'Tinjau data dan kecocokan judul pengajuan')

@section('content')
<div class="space-y-5">
    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <a href="{{ route('prodi.pengajuan-tugas-akhir') }}" class="text-sm font-semibold text-[#841719] hover:underline">← Kembali ke daftar pengajuan</a>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-gray-100 pb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Informasi Mahasiswa</p>
                <h1 class="mt-1 text-lg font-bold">{{ $tugasAkhir->mahasiswa->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">NIM {{ $tugasAkhir->mahasiswa->nim ?: '-' }}</p>
            </div>
            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">{{ $tugasAkhir->status }}</span>
        </div>

        <div class="grid gap-5 py-5 md:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Judul Tugas Akhir</p>
                <h2 class="mt-2 text-base font-bold">{{ $tugasAkhir->judul }}</h2>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Topik</p>
                <p class="mt-1 text-sm">{{ $tugasAkhir->topik }}</p>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Deskripsi</p>
                <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tugasAkhir->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4">
                <h2 class="font-bold">Calon Pembimbing yang Diusulkan</h2>
                <div class="mt-3 space-y-3 text-sm">
                    <p><span class="font-semibold">Calon 1:</span> {{ $tugasAkhir->calonDosen1?->name ?? '-' }}</p>
                    <p><span class="font-semibold">Calon 2:</span> {{ $tugasAkhir->calonDosen2?->name ?? '-' }}</p>
                    <p><span class="font-semibold">Calon 3:</span> {{ $tugasAkhir->calonDosen3?->name ?? '-' }}</p>
                </div>
                @if($tugasAkhir->catatan_prodi)
                    <div class="mt-4 border-t border-gray-200 pt-3">
                        <p class="text-xs font-semibold text-gray-500">Catatan sebelumnya</p>
                        <p class="mt-1 whitespace-pre-line text-sm">{{ $tugasAkhir->catatan_prodi }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div>
            <h2 class="font-bold">Pemeriksaan Kemiripan Judul</h2>
            <p class="mt-1 text-xs text-gray-500">Hasil ini hanya membantu pemeriksaan manual, bukan keputusan otomatis.</p>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[560px] text-left text-sm">
                <thead class="bg-gray-50 text-xs text-gray-600">
                    <tr><th class="p-3">Judul Pembanding</th><th class="p-3">Mahasiswa</th><th class="p-3">Kemiripan</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($judulSerupa as $item)
                        <tr>
                            <td class="p-3">{{ $item['judul'] }}</td>
                            <td class="p-3">{{ $item['mahasiswa'] }}</td>
                            <td class="p-3">{{ $item['similarity'] }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-5 text-center text-gray-500">Tidak ditemukan judul serupa dengan tingkat kemiripan 80% atau lebih.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($tugasAkhir->status === 'Menunggu')
        <form action="{{ route('prodi.pengajuan-tugas-akhir.update', $tugasAkhir) }}" method="POST" class="rounded-xl border border-gray-200 bg-white p-5">
            @csrf
            @method('PUT')
            <label for="catatan_prodi" class="block text-sm font-semibold">Catatan Prodi <span class="text-gray-500">(wajib jika meminta perbaikan)</span></label>
            <textarea id="catatan_prodi" name="catatan_prodi" rows="3" maxlength="2000"
                      class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('catatan_prodi', $tugasAkhir->catatan_prodi) }}</textarea>
            <div class="mt-4 flex flex-wrap justify-end gap-3">
                <button type="submit" name="status" value="Perlu Perbaikan" class="rounded-lg border border-[#596329] px-4 py-2 text-sm font-semibold text-[#596329] hover:bg-green-50">
                    Minta Perbaikan
                </button>
                <button type="submit" name="status" value="Disetujui" class="rounded-lg bg-[#841719] px-4 py-2 text-sm font-semibold text-white hover:bg-[#681315]">
                    Setujui Judul
                </button>
            </div>
        </form>
    @elseif($tugasAkhir->status === 'Perlu Perbaikan')
        <div class="rounded-xl border border-orange-200 bg-orange-50 p-5 text-sm text-orange-800">
            Pengajuan sedang menunggu mahasiswa mengirimkan perbaikan. Prodi dapat memeriksa kembali setelah pengajuan dikirim ulang.
        </div>
    @endif
</div>
@endsection
