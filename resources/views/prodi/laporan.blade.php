@extends('layouts.prodi')

@section('title', 'Laporan Tugas Akhir - SIMTA')
@section('page-label', 'Laporan Tugas Akhir')
@section('page-description', 'Ringkasan tahapan dan hasil tugas akhir')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white p-5">
        <div>
            <h1 class="text-lg font-bold">Laporan Pelaksanaan Tugas Akhir</h1>
            <p class="mt-1 text-sm text-gray-500">Ringkasan berdasarkan data pengajuan, jadwal, dan hasil yang tercatat.</p>
        </div>
        <a href="{{ route('prodi.laporan.export') }}" class="rounded-lg bg-[#841719] px-4 py-2 text-sm font-semibold text-white hover:bg-[#681315]">
            Unduh CSV
        </a>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @foreach([
            ['Total Pengajuan', $summary['total']],
            ['Menunggu Pemeriksaan', $summary['menunggu']],
            ['Perlu Perbaikan', $summary['perlu_perbaikan']],
            ['Pembimbing Ditetapkan', $summary['pembimbing']],
            ['Sempro Lulus', $summary['sempro_lulus']],
            ['Sidang/Komprehensif Lulus', $summary['sidang_lulus']],
        ] as [$label, $count])
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold">{{ $count }}</p>
            </div>
        @endforeach
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-bold">Sebaran Progres Mahasiswa</h2>
        <div class="mt-4 space-y-3">
            @forelse($progressDistribution as $progress => $count)
                <div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span>{{ $progress }}</span>
                        <span class="font-semibold">{{ $count }}</span>
                    </div>
                    <div class="mt-1 h-2 rounded-full bg-gray-100">
                        <div class="h-2 rounded-full bg-[#596329]" style="width: {{ $summary['total'] ? ($count / $summary['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada data untuk dilaporkan.</p>
            @endforelse
        </div>
        <a href="{{ route('prodi.progres-mahasiswa') }}" class="mt-5 inline-block text-sm font-semibold text-[#841719] hover:underline">
            Buka detail progres mahasiswa →
        </a>
    </section>
</div>
@endsection
