@extends('layouts.dosen')

@section('title', 'Jadwal Dosen - SIMTA')
@section('page-label', 'Jadwal')
@section('page-description', 'Jadwal Sempro dan Sidang yang melibatkan Anda')

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
            @if($jenis !== 'semua')
                <input type="hidden" name="jenis" value="{{ $jenis }}">
            @endif
            <input type="search" name="q" value="{{ $search }}" placeholder="Nama, NIM, atau judul"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-[#596329] px-4 py-2 text-sm font-semibold text-white">Cari</button>
        </form>
    </div>
    <nav class="mt-5 flex flex-wrap gap-2" aria-label="Filter jadwal">
        @foreach(['semua' => 'Semua', 'sempro' => 'Sempro', 'sidang' => 'Sidang / Komprehensif'] as $value => $label)
            <a href="{{ route('dosen.jadwal', array_filter(['jenis' => $value === 'semua' ? null : $value, 'q' => $search])) }}"
               @class([
                   'rounded-lg border px-5 py-2 text-sm font-semibold',
                   'border-[#841719] bg-[#841719] text-white' => $jenis === $value,
                   'border-gray-300 bg-white text-gray-700 hover:border-[#841719]' => $jenis !== $value,
               ])>
                {{ $label }}
            </a>
        @endforeach
    </nav>
    <div class="mt-5 overflow-x-auto">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="bg-gray-50 text-xs text-gray-600">
                <tr>
                    <th class="p-3">No</th><th class="p-3">Kegiatan</th><th class="p-3">Mahasiswa</th>
                    <th class="p-3">Tanggal &amp; Waktu</th><th class="p-3">Lokasi</th>
                    <th class="p-3">Peran Saya</th><th class="p-3">Status</th><th class="p-3">Nilai / Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($jadwal as $item)
                    <tr>
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $item->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro' }}</td>
                        <td class="p-3">
                            @if($item->tugasAkhir)
                                <a href="{{ route('dosen.mahasiswa.show', $item->tugasAkhir) }}" class="font-semibold hover:text-[#841719] hover:underline">{{ $item->mahasiswa->name }}</a>
                            @else
                                {{ $item->mahasiswa->name }}
                            @endif
                        </td>
                        <td class="whitespace-nowrap p-3">{{ $item->waktu?->format('d M Y, H.i') ?? 'Belum tersedia' }}</td>
                        <td class="p-3">{{ $item->lokasi ?? '-' }}</td>
                        <td class="p-3">{{ $item->peran_dosen }}</td>
                        <td class="p-3">
                            <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-800">{{ ucfirst($item->status) }}</span>
                        </td>
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
