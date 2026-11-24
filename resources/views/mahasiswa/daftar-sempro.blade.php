
@extends('layouts.app')

@section('title', 'Daftar Sempro - SIMTA')
@section('page-label', 'Daftar Seminar Proposal')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center flex-wrap gap-3">
        <h2 class="text-2xl font-bold">
            Daftar Sempro
        </h2>

        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('tugas-akhir.ajukan-judul') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-medium hover:bg-gray-300">
                Ajukan Judul
            </a>

            <a href="{{ route('tugas-akhir.daftar-sidang') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-medium hover:bg-gray-300">
                Daftar Sidang
            </a>
        </div>
    </div>

    {{-- NOTIFIKASI BERHASIL --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- PROGRES TUGAS AKHIR --}}
    <div class="bg-white rounded-xl border p-6">

        <h3 class="font-bold mb-6">
            Progres Tugas Akhir Saya
        </h3>

        <div class="grid grid-cols-4 text-center gap-2">

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-black flex items-center justify-center">
                    1
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    Pengajuan Judul
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-black flex items-center justify-center">
                    2
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    Penetapan Pembimbing
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-green-700 text-white flex items-center justify-center">
                    3
                </div>
                <p class="mt-2 text-sm font-semibold text-green-700">
                    Seminar Proposal
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-black flex items-center justify-center">
                    4
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    Sidang
                </p>
            </div>

        </div>
    </div>

    {{-- DAFTAR SEPRO --}}
    <div class="bg-white rounded-xl border p-6">

        <h3 class="text-lg font-bold mb-4">
            Daftar Seminar Proposal
        </h3>

        @if($sempro->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">No</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Judul</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Topik</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Status</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Diperbarui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($sempro as $item)
                            <tr class="border-b last:border-0">
                                <td class="py-3 px-4">{{ $no++ }}</td>
                                <td class="py-3 px-4">{{ $item->judul ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $item->topik ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        {{ $item->status == 'Disetujui' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $item->status ?? 'Menunggu' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-500">
                                    {{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">
                    Belum ada data seminar proposal.
                </p>
            </div>
        @endif

    </div>

    {{-- DETAIL TUGAS AKHIR --}}
    <div class="bg-white rounded-xl border p-6">

        <h3 class="text-lg font-bold mb-4">
            Detail Tugas Akhir
        </h3>

        <table class="w-full">

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Judul
                </td>
                <td>
                    {{ $tugasAkhir->judul ?? '-' }}
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Topik
                </td>
                <td>
                    {{ $tugasAkhir->topik ?? '-' }}
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Pembimbing I
                </td>
                <td>
                    -
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Pembimbing II
                </td>
                <td>
                    -
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Status
                </td>
                <td>
                    <span class="bg-red-700 text-white px-3 py-1 rounded">
                        {{ $tugasAkhir->status ?? 'Belum Ada' }}
                    </span>
                </td>
            </tr>

            <tr>
                <td class="py-3 font-semibold">
                    Catatan Prodi
                </td>
                <td>
                    -
                </td>
            </tr>

        </table>

    </div>

</div>

@endsection
