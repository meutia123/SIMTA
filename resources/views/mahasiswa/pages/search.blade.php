@extends('layouts.app')

@section('title', 'Hasil Pencarian - SIMTA')
@section('page-label', 'Hasil Pencarian')

@section('content')
<div class="space-y-5">
    <p class="text-sm text-gray-600">Hasil untuk <strong>{{ $query }}</strong></p>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-bold">Tugas Akhir</h2>
        @forelse($tugasAkhir as $item)
            <a href="{{ route('tugas-akhir') }}" class="mt-3 block rounded border-b border-gray-100 py-3 last:border-0">
                <p class="font-semibold">{{ $item->judul }}</p>
                <p class="text-sm text-gray-500">{{ $item->topik }}</p>
            </a>
        @empty
            <p class="mt-3 text-sm text-gray-500">Tidak ada hasil.</p>
        @endforelse
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-bold">Dokumen</h2>
        @forelse($documents as $item)
            <a href="{{ route('dokumen.download', $item) }}" class="mt-3 block rounded border-b border-gray-100 py-3 last:border-0">
                <p class="font-semibold">{{ $item->nama_dokumen }}</p>
                <p class="text-sm text-gray-500">{{ $item->jenis_dokumen }}</p>
            </a>
        @empty
            <p class="mt-3 text-sm text-gray-500">Tidak ada hasil.</p>
        @endforelse
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-bold">Jadwal</h2>
        @forelse($jadwal as $item)
            <a href="{{ route('jadwal') }}" class="mt-3 block rounded border-b border-gray-100 py-3 last:border-0">
                <p class="font-semibold">{{ ucfirst($item->jenis) }} — {{ $item->lokasi ?? 'Menunggu jadwal' }}</p>
                <p class="text-sm text-gray-500">{{ $item->waktu?->format('d M Y, H.i') ?? ucfirst($item->status) }}</p>
            </a>
        @empty
            <p class="mt-3 text-sm text-gray-500">Tidak ada hasil.</p>
        @endforelse
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-bold">Pengumuman</h2>
        @forelse($pengumuman as $item)
            <a href="{{ route('pengumuman') }}" class="mt-3 block rounded border-b border-gray-100 py-3 last:border-0">
                <p class="font-semibold">{{ $item->judul }}</p>
                <p class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($item->isi, 140) }}</p>
            </a>
        @empty
            <p class="mt-3 text-sm text-gray-500">Tidak ada hasil.</p>
        @endforelse
    </section>
</div>
@endsection
