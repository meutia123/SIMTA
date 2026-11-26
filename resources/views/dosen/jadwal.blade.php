@extends('layouts.dosen')

@section('title', 'Jadwal Dosen - SIMTA')
@section('page-label', 'Jadwal Dosen')

@section('content')
<section class="rounded-xl border border-gray-200 bg-white p-5">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-bold">Jadwal Sempro dan Sidang/Komprehensif</h1>
            <p class="mt-1 text-sm text-gray-500">Jadwal yang melibatkan Anda sebagai dosen pembimbing atau penguji.</p>
        </div>
        <form action="{{ route('dosen.jadwal') }}" method="GET" class="flex gap-2">
            <input type="search" name="q" value="{{ $search }}" placeholder="Nama, NIM, atau judul"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-[#596329] px-4 py-2 text-sm font-semibold text-white">Cari</button>
        </form>
    </div>
    <div class="mt-5 overflow-x-auto">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="bg-gray-50 text-xs text-gray-600">
                <tr>
                    <th class="p-3">Jadwal</th><th class="p-3">Tahap</th><th class="p-3">Mahasiswa</th>
                    <th class="p-3">Judul</th><th class="p-3">Lokasi</th><th class="p-3">Tim Penguji</th><th class="p-3">Status</th>
                    <th class="p-3">Nilai / Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($jadwal as $item)
                    <tr>
                        <td class="whitespace-nowrap p-3">{{ $item->waktu?->format('d M Y, H.i') ?? 'Belum tersedia' }}</td>
                        <td class="p-3">{{ $item->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro' }}</td>
                        <td class="p-3">{{ $item->mahasiswa->name }}</td>
                        <td class="p-3">{{ $item->tugasAkhir?->judul ?? '-' }}</td>
                        <td class="p-3">{{ $item->lokasi ?? '-' }}</td>
                        <td class="p-3">{{ $item->penguji->pluck('name')->implode(', ') ?: ($item->dosen_penguji ?? '-') }}</td>
                        <td class="p-3">{{ ucfirst($item->status) }}</td>
                        <td class="p-3">
                            @if($item->status !== 'selesai')
                                <span class="text-gray-500">Menunggu ujian selesai</span>
                            @elseif($item->status_validasi_nilai === 'menunggu_validasi')
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Menunggu Validasi Prodi</span>
                            @elseif($item->nilaiTervalidasi())
                                <span class="font-semibold text-green-700">Tervalidasi · {{ number_format((float) $item->nilai, 2) }}</span>
                            @elseif($item->status_validasi_nilai === 'dikembalikan' && (int) $item->dosen_penilai_id !== (int) Auth::id())
                                <span class="text-orange-700">Dikembalikan ke dosen penilai</span>
                            @else
                                <a href="{{ route('dosen.nilai', $item) }}"
                                   class="inline-flex rounded bg-[#841719] px-3 py-1.5 font-semibold text-white">
                                    {{ $item->status_validasi_nilai === 'dikembalikan' ? 'Perbaiki Nilai' : 'Input Nilai' }}
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-8 text-center text-gray-500">Tidak ada jadwal yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
