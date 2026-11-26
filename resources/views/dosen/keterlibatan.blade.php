@extends('layouts.dosen')

@section('title', 'Rekap Keterlibatan - SIMTA')
@section('page-label', 'Rekap Keterlibatan')

@section('content')
<div class="space-y-5">
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h1 class="text-lg font-bold">Rekap Keterlibatan Saya</h1>
        <p class="mt-1 text-sm text-gray-500">Ringkasan mahasiswa bimbingan dan ujian yang menugaskan Anda sebagai penguji.</p>
        <div class="mt-4 grid gap-3 sm:grid-cols-3">
            @foreach([
                ['Mahasiswa Bimbingan', $summary['bimbingan']],
                ['Penguji Sempro', $summary['sempro']],
                ['Penguji Sidang/Komprehensif', $summary['sidang']],
            ] as [$label, $count])
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ $count }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-bold">Mahasiswa Bimbingan</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[600px] text-left text-sm">
                <thead class="bg-gray-50 text-xs text-gray-600">
                    <tr><th class="p-3">Mahasiswa</th><th class="p-3">Judul</th><th class="p-3">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($mahasiswaBimbingan as $item)
                        <tr>
                            <td class="p-3">{{ $item->mahasiswa->name }}</td>
                            <td class="p-3">{{ $item->judul }}</td>
                            <td class="p-3">{{ $item->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-6 text-center text-gray-500">Belum ada data bimbingan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-bold">Keterlibatan sebagai Penguji</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-gray-50 text-xs text-gray-600">
                    <tr><th class="p-3">Tanggal</th><th class="p-3">Tahap</th><th class="p-3">Mahasiswa</th><th class="p-3">Judul</th><th class="p-3">Hasil</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengujian as $item)
                        <tr>
                            <td class="p-3">{{ $item->waktu?->format('d M Y, H.i') ?? '-' }}</td>
                            <td class="p-3">{{ $item->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro' }}</td>
                            <td class="p-3">{{ $item->mahasiswa->name }}</td>
                            <td class="p-3">{{ $item->tugasAkhir?->judul ?? '-' }}</td>
                            <td class="p-3">
                                @if($item->status_validasi_nilai === 'menunggu_validasi')
                                    Menunggu Validasi Prodi
                                @elseif($item->status_validasi_nilai === 'dikembalikan')
                                    Nilai Dikembalikan
                                @elseif($item->nilaiTervalidasi())
                                    {{ $item->hasil }} · {{ number_format((float) $item->nilai, 2) }}
                                @else
                                    Belum dicatat
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-500">Belum ada penugasan sebagai penguji.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
