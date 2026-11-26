@extends('layouts.prodi')

@section('title', 'Laporan Tugas Akhir - SIMTA')
@section('page-label', 'Laporan')
@section('page-description', 'Ringkasan pelaksanaan tugas akhir')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <form action="{{ route('prodi.laporan') }}" method="GET" class="w-full max-w-xs">
            <label for="periode" class="block text-sm font-semibold">Periode</label>
            <select id="periode" name="periode" onchange="this.form.submit()"
                    class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm">
                @forelse($periods as $value => $label)
                    <option value="{{ $value }}" {{ $periode === $value ? 'selected' : '' }}>{{ $label }}</option>
                @empty
                    <option value="">Belum ada periode ujian</option>
                @endforelse
            </select>
        </form>

        <a href="{{ route('prodi.laporan.export', $periode ? ['periode' => $periode] : []) }}"
           class="inline-flex items-center gap-2 rounded-md bg-[#841719] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#681315]">
            <span aria-hidden="true">⬇</span> Unduh Laporan
        </a>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Total Mahasiswa', $summary['total'], 'bg-indigo-100 text-indigo-700', '👥'],
            ['Judul Disetujui', $summary['disetujui'], 'bg-amber-100 text-amber-700', '▤'],
            ['Lulus Sempro', $summary['sempro_lulus'], 'bg-green-100 text-green-700', '◷'],
            ['Lulus Sidang', $summary['sidang_lulus'], 'bg-red-100 text-red-700', '✓'],
        ] as [$label, $count, $color, $icon])
            <article class="flex items-center gap-3 rounded-lg border border-gray-300 bg-white p-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-md {{ $color }}" aria-hidden="true">{{ $icon }}</span>
                <div>
                    <p class="text-xl font-bold">{{ $count }}</p>
                    <p class="text-xs text-gray-500">{{ $label }}</p>
                </div>
            </article>
        @endforeach
    </section>

    <section class="rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="font-bold">
            Jumlah Mahasiswa Per Tahap
            @if($periode)
                - {{ $periods[$periode] }}
            @endif
        </h2>
        <div class="mt-5 space-y-4">
            @foreach($stageCounts as $stage)
                <div class="grid items-center gap-3 text-sm sm:grid-cols-[150px_minmax(0,1fr)_36px]">
                    <span class="font-medium">{{ $stage['label'] }}</span>
                    <div class="h-2.5 rounded-full bg-indigo-100" role="progressbar"
                         aria-label="{{ $stage['label'] }}"
                         aria-valuemin="0" aria-valuemax="{{ $maxStageCount }}" aria-valuenow="{{ $stage['count'] }}">
                        <div class="h-2.5 rounded-full bg-blue-600"
                             style="width: {{ ($stage['count'] / $maxStageCount) * 100 }}%"></div>
                    </div>
                    <span class="text-right font-semibold">{{ $stage['count'] }}</span>
                </div>
            @endforeach
        </div>
        @if($summary['total'] === 0)
            <p class="mt-5 text-sm text-gray-500">Belum ada data untuk periode ini.</p>
        @endif
    </section>
</div>
@endsection
