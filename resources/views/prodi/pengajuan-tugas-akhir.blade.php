@extends('layouts.prodi')

@section('title', 'Pengajuan Judul - SIMTA')
@section('page-label', 'Pengajuan Judul')
@section('page-description', 'Periksa judul dan tetapkan dosen pembimbing')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-lg font-bold">Daftar Pengajuan Judul</h1>
                <p class="mt-1 text-sm text-gray-500">Pilih pengajuan untuk memeriksa judul atau menetapkan pembimbing.</p>
            </div>
            <form action="{{ route('prodi.pengajuan-tugas-akhir') }}" method="GET" class="flex gap-2">
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari nama, NIM, judul"
                       class="w-52 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <button class="rounded-lg bg-[#596329] px-4 py-2 text-sm font-semibold text-white">Cari</button>
            </form>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            @foreach([
                'semua' => 'Semua',
                'Menunggu' => 'Menunggu',
                'Disetujui' => 'Disetujui',
                'Perlu Perbaikan' => 'Perlu Perbaikan',
                'Ditolak' => 'Ditolak',
            ] as $status => $label)
                <a href="{{ route('prodi.pengajuan-tugas-akhir', array_filter(['status' => $status, 'q' => $filters['q']])) }}"
                   class="rounded-full border px-3 py-1.5 text-xs font-semibold {{ $filters['status'] === $status ? 'border-[#841719] bg-[#841719] text-white' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }}">
                    {{ $label }} <span class="{{ $filters['status'] === $status ? 'text-red-100' : 'text-gray-400' }}">({{ $status === 'semua' ? $statusCounts->sum() : ($statusCounts[$status] ?? 0) }})</span>
                </a>
            @endforeach
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Mahasiswa</th>
                        <th class="p-3">Judul</th>
                        <th class="p-3">Calon Pembimbing</th>
                        <th class="p-3">Pembimbing Ditetapkan</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengajuan as $item)
                        <tr>
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">
                                <p class="font-semibold text-gray-800">{{ $item->mahasiswa->name }}</p>
                                <p class="mt-1 text-gray-500">{{ $item->mahasiswa->nim ?: '-' }}</p>
                            </td>
                            <td class="p-3">
                                <p class="max-w-xs font-semibold text-gray-800">{{ $item->judul }}</p>
                                <p class="mt-1 text-gray-500">{{ $item->topik }}</p>
                            </td>
                            <td class="p-3">{{ $item->calonDosen1?->name ?? '-' }}<br>{{ $item->calonDosen2?->name ?? '-' }}</td>
                            <td class="p-3">{{ $item->dosenPembimbing1?->name ?? '-' }}<br>{{ $item->dosenPembimbing2?->name ?? '-' }}</td>
                            <td class="p-3">
                                <span class="rounded-full px-2.5 py-1 font-semibold {{ $item->status === 'Disetujui' ? 'bg-green-100 text-green-700' : ($item->status === 'Perlu Perbaikan' ? 'bg-orange-100 text-orange-700' : ($item->status === 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="flex flex-col items-start gap-2">
                                    @if($item->status === 'Menunggu')
                                        <a href="{{ route('prodi.pengajuan-tugas-akhir.review', $item) }}" class="rounded bg-[#841719] px-3 py-1.5 font-semibold text-white hover:bg-[#681315]">
                                            Periksa
                                        </a>
                                    @elseif($item->status === 'Disetujui' && (!$item->dosen_pembimbing_1 || !$item->dosen_pembimbing_2))
                                        <a href="{{ route('prodi.pengajuan-tugas-akhir.pembimbing', $item) }}" class="rounded bg-[#596329] px-3 py-1.5 font-semibold text-white hover:bg-[#454e20]">
                                            Tetapkan
                                        </a>
                                    @elseif($item->status === 'Disetujui')
                                        <a href="{{ route('prodi.pengajuan-tugas-akhir.pembimbing', $item) }}" class="font-semibold text-[#596329] hover:underline">
                                            Ubah Pembimbing
                                        </a>
                                    @else
                                        <a href="{{ route('prodi.pengajuan-tugas-akhir.review', $item) }}" class="font-semibold text-[#841719] hover:underline">
                                            Detail
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-gray-500">Tidak ada pengajuan untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
