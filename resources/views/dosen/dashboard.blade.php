@extends('layouts.dosen')

@section('title', 'Dashboard Dosen - SIMTA')
@section('page-label', 'Dashboard Dosen')
@section('page-description', 'Ringkasan mahasiswa dan jadwal Anda')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <section class="flex flex-wrap items-center justify-between gap-4 rounded-lg bg-[#841719] p-5 text-white">
        <div>
            <p class="text-sm text-red-100">Selamat Datang</p>
            <h1 class="mt-1 text-xl font-bold">Halo, {{ Auth::user()->name }}!</h1>
            <p class="mt-2 text-sm text-red-100">Pantau mahasiswa, jadwal Sempro dan Sidang, serta keterlibatanmu di sini.</p>
        </div>
        <a href="{{ route('dosen.mahasiswa') }}"
           class="rounded-md bg-white px-5 py-2.5 text-sm font-bold text-blue-700 hover:bg-blue-50">
            ♧ &nbsp; Lihat Mahasiswa Saya
        </a>
    </section>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Mahasiswa Saya', $mahasiswaSaya, 'dosen.mahasiswa', 'bg-indigo-100 text-indigo-700', '♧'],
            ['Penguji Sempro', $pengujiSempro, 'dosen.jadwal', 'bg-amber-100 text-amber-700', '▤'],
            ['Penguji Sidang', $pengujiSidang, 'dosen.jadwal', 'bg-green-100 text-green-700', '✓'],
            ['Jadwal Hari Ini', $jadwalHariIni, 'dosen.jadwal', 'bg-red-100 text-red-700', '▦'],
        ] as [$label, $count, $routeName, $color, $icon])
            <a href="{{ route($routeName) }}" class="flex items-center gap-3 rounded-lg border border-gray-300 bg-white p-4 hover:border-[#841719]">
                <span class="flex h-10 w-10 items-center justify-center rounded-md {{ $color }}" aria-hidden="true">{{ $icon }}</span>
                <span>
                    <span class="block text-xl font-bold">{{ $count }}</span>
                    <span class="block text-xs text-gray-500">{{ $label }}</span>
                </span>
            </a>
        @endforeach
    </section>

    <div class="grid gap-4 xl:grid-cols-[minmax(0,2fr)_minmax(250px,0.8fr)]">
        <section class="rounded-lg border border-gray-200 bg-white p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-gray-500">Mahasiswa Terbaru</h2>
                    <p class="mt-1 text-xs text-gray-500">Mahasiswa bimbingan dan ujian yang melibatkan Anda.</p>
                </div>
                <a href="{{ route('dosen.mahasiswa') }}" class="text-xs font-semibold text-[#841719] hover:underline">Semua mahasiswa →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-xs">
                    <thead class="bg-[#f5f3fb] text-gray-700">
                        <tr>
                            <th class="p-3">No</th><th class="p-3">Nama</th><th class="p-3">Judul Tugas Akhir</th>
                            <th class="p-3">Peran</th><th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($mahasiswaTerbaru as $row)
                            @php
                                $statusClass = match($row['status']) {
                                    'Sempro' => 'bg-amber-300 text-amber-900',
                                    'Sidang' => 'bg-green-400 text-green-900',
                                    'Pembimbing Ditetapkan' => 'bg-indigo-400 text-indigo-950',
                                    default => 'bg-gray-400 text-white',
                                };
                            @endphp
                            <tr>
                                <td class="p-3">{{ $loop->iteration }}</td>
                                <td class="p-3 font-semibold">{{ $row['tugasAkhir']->mahasiswa->name }}</td>
                                <td class="p-3">{{ $row['tugasAkhir']->judul }}</td>
                                <td class="p-3">{{ implode(', ', $row['roles']) }}</td>
                                <td class="p-3"><span class="rounded-md px-2.5 py-1 font-semibold {{ $statusClass }}">{{ $row['status'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-gray-500">Belum ada mahasiswa yang melibatkan Anda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="space-y-4">
            <section class="rounded-lg border border-gray-300 bg-white p-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold">Jadwal Terdekat</h2>
                    <a href="{{ route('dosen.jadwal') }}" class="text-xs font-semibold text-[#841719]">Lihat semua</a>
                </div>
                <div class="mt-3 divide-y divide-gray-200">
                    @forelse($jadwalMendatang->take(3) as $item)
                        <div class="flex gap-3 py-3">
                            <div class="flex h-10 w-10 shrink-0 flex-col items-center justify-center rounded-md bg-indigo-100 text-[10px] text-indigo-700">
                                <span>{{ $item->waktu?->format('d') ?? '-' }}</span>
                                <span>{{ $item->waktu?->format('M') }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold">{{ $item->jenis === 'sidang' ? 'Sidang' : 'Sempro' }} - {{ $item->mahasiswa->name }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $item->waktu?->format('H.i') }} · {{ $item->lokasi ?? 'Lokasi belum tersedia' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-xs text-gray-500">Tidak ada jadwal mendatang.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-gray-300 bg-white p-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold">Pengumuman</h2>
                    <a href="{{ route('dosen.dokumen') }}" class="text-xs font-semibold text-[#841719]">Dokumen</a>
                </div>
                <div class="mt-3 divide-y divide-gray-200">
                    @forelse($pengumuman as $item)
                        <div class="flex gap-3 py-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-purple-100 text-purple-700">♧</span>
                            <div>
                                <p class="text-xs font-semibold">{{ $item->judul }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $item->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-xs text-gray-500">Belum ada pengumuman.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    @if($suratKeputusan->isNotEmpty())
        <section class="rounded-lg border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-bold">SK Terkait</h2>
                <a href="{{ route('dosen.dokumen') }}" class="text-xs font-semibold text-[#841719] hover:underline">Semua SK &amp; dokumen →</a>
            </div>
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
