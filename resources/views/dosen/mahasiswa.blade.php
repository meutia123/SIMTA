@extends('layouts.dosen')

@section('title', 'Mahasiswa Saya - SIMTA')
@section('page-label', 'Mahasiswa Saya')
@section('page-description', 'Daftar mahasiswa yang Anda bimbing atau uji')

@section('content')
<section class="rounded-xl border border-gray-200 bg-white p-5">
    <h1 class="text-lg font-bold">Mahasiswa Saya</h1>
    <p class="mt-1 text-sm text-gray-500">Mahasiswa yang dibimbing atau melibatkan Anda sebagai penguji.</p>

    <nav class="mt-5 flex flex-wrap gap-2" aria-label="Filter mahasiswa">
        @foreach(['semua' => 'Semua', 'pembimbing' => 'Pembimbing', 'penguji' => 'Penguji'] as $value => $label)
            <a href="{{ route('dosen.mahasiswa', array_filter(['jenis' => $value === 'semua' ? null : $value, 'q' => $search])) }}"
               @class([
                   'rounded-lg border px-5 py-2 text-sm font-semibold',
                   'border-[#841719] bg-[#841719] text-white' => $jenis === $value,
                   'border-gray-300 bg-white text-gray-700 hover:border-[#841719]' => $jenis !== $value,
               ])>
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <form action="{{ route('dosen.mahasiswa') }}" method="GET" class="mt-4 flex gap-2">
        @if($jenis !== 'semua')
            <input type="hidden" name="jenis" value="{{ $jenis }}">
        @endif
        <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama, NIM, atau judul"
               class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <button class="rounded-lg bg-[#596329] px-4 py-2 text-sm font-semibold text-white">Cari</button>
    </form>

    <div class="mt-5 overflow-x-auto">
        <table class="w-full min-w-[850px] text-left text-xs">
            <thead class="bg-[#f5f3fb] text-gray-700">
                <tr>
                    <th class="p-3">No</th><th class="p-3">Nama</th><th class="p-3">NIM</th>
                    <th class="p-3">Judul Tugas Akhir</th><th class="p-3">Peran</th><th class="p-3">Status</th><th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($rows as $row)
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
                        <td class="p-3">{{ $row['tugasAkhir']->mahasiswa->nim ?: '-' }}</td>
                        <td class="p-3">{{ $row['tugasAkhir']->judul }}</td>
                        <td class="p-3">{{ implode(', ', $row['roles']) }}</td>
                        <td class="p-3"><span class="rounded-md px-2.5 py-1 font-semibold {{ $statusClass }}">{{ $row['status'] }}</span></td>
                        <td class="p-3">
                            <a href="{{ route('dosen.mahasiswa.show', $row['tugasAkhir']) }}"
                               class="rounded-md border border-gray-300 px-3 py-1.5 font-semibold hover:bg-gray-50">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-8 text-center text-gray-500">Belum ada mahasiswa pada daftar ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
