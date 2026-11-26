@extends('layouts.dosen')

@section('title', 'Dashboard Dosen - SIMTA')
@section('page-label', 'Dashboard Dosen')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-[#841719] p-6 text-white">
        <div>
            <p class="text-sm">Selamat datang,</p>
            <h1 class="mt-1 text-2xl font-bold">{{ Auth::user()->name }}</h1>
            <p class="mt-2 text-sm text-red-100">Jadwal Sempro/Sidang, mahasiswa bimbingan, dan keterlibatan Anda.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('dosen.jadwal') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-[#841719]">Lihat Jadwal</a>
            <a href="{{ route('dosen.keterlibatan') }}" class="rounded-lg border border-white px-4 py-2 text-sm font-semibold text-white">Rekap Keterlibatan</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Mahasiswa Bimbingan', $mahasiswaSaya],
            ['Penguji Sempro', $pengujiSempro],
            ['Penguji Sidang/Komprehensif', $pengujiSidang],
            ['Jadwal Hari Ini', $jadwalHariIni],
        ] as [$label, $count])
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold">{{ $count }}</p>
            </div>
        @endforeach
    </div>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold">Jadwal Ujian Saya</h2>
                <p class="mt-1 text-xs text-gray-500">Jadwal yang melibatkan Anda sebagai pembimbing atau penguji.</p>
            </div>
            <a href="{{ route('dosen.jadwal') }}" class="text-xs font-semibold text-[#841719] hover:underline">Semua jadwal →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Tahap</th>
                        <th class="p-3">Mahasiswa</th>
                        <th class="p-3">Judul</th>
                        <th class="p-3">Peran / Tim Penguji</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jadwalMendatang as $item)
                        <tr>
                            <td class="whitespace-nowrap p-3">{{ $item->waktu?->format('d M Y, H.i') ?? '-' }}</td>
                            <td class="p-3">{{ $item->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro' }}</td>
                            <td class="p-3">{{ $item->mahasiswa->name }}</td>
                            <td class="p-3">{{ $item->tugasAkhir?->judul ?? '-' }}</td>
                            <td class="p-3">
                                {{ $item->penguji->pluck('name')->implode(', ') ?: ($item->dosen_penguji ?? 'Pembimbing') }}
                            </td>
                            <td class="p-3">{{ ucfirst($item->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-gray-500">Tidak ada jadwal mendatang yang melibatkan Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold">Mahasiswa Bimbingan</h2>
                <p class="mt-1 text-xs text-gray-500">Daftar mahasiswa yang ditetapkan kepada Anda.</p>
            </div>
            <a href="{{ route('dosen.keterlibatan') }}" class="text-xs font-semibold text-[#841719] hover:underline">Rekap keterlibatan →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr><th class="p-3">No</th><th class="p-3">Mahasiswa</th><th class="p-3">Judul</th><th class="p-3">Status Pengajuan</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($mahasiswaTerbaru as $item)
                        <tr>
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">{{ $item->mahasiswa->name }}</td>
                            <td class="p-3">{{ $item->judul }}</td>
                            <td class="p-3">{{ $item->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-gray-500">Belum ada mahasiswa bimbingan yang ditetapkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($suratKeputusan->isNotEmpty())
        <section class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="font-semibold text-gray-800">SK Terkait</h2>
            <div class="mt-3 divide-y divide-gray-100">
                @foreach($suratKeputusan as $surat)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                        <div>
                            <p class="text-sm font-semibold">{{ $surat['label'] }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $surat['mahasiswa'] }}</p>
                        </div>
                        <a href="{{ route('surat-keputusan.download', ['jenis' => $surat['jenis'], 'id' => $surat['id']]) }}"
                           class="text-sm font-semibold text-[#841719] hover:underline">Unduh</a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
