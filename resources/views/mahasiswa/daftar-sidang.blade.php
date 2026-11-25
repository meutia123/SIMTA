
@extends('layouts.app')

@section('title', 'Daftar Sidang - SIMTA')
@section('page-label', 'Daftar Sidang')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center flex-wrap gap-3">
        <h2 class="text-2xl font-bold">
            Daftar Sidang
        </h2>

        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('tugas-akhir.ajukan-judul') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-medium hover:bg-gray-300">
                Ajukan Judul
            </a>

            <a href="{{ route('tugas-akhir.daftar-sempro') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-medium hover:bg-gray-300">
                Daftar Sempro
            </a>
        </div>
    </div>

    {{-- NOTIFIKASI BERHASIL --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-lg">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if(!$tugasAkhir)
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
            Ajukan judul tugas akhir sebelum mendaftar Sidang.
            <a href="{{ route('tugas-akhir.ajukan-judul') }}" class="font-semibold underline">Ajukan judul</a>
        </div>
    @elseif($tugasAkhir->status !== 'Disetujui')
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
            Pendaftaran Sidang tersedia setelah Prodi menyetujui pengajuan judul.
        </div>
    @elseif(!$sempro->contains(fn ($item) => $item->status === 'selesai'))
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
            Seminar Proposal harus ditandai selesai oleh Prodi sebelum mendaftar Sidang.
        </div>
    @elseif(!$adaPendaftaranAktif)
        <form action="{{ route('tugas-akhir.daftar-sidang.store') }}" method="POST">
            @csrf
            <button type="submit" class="rounded-lg bg-[#841719] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6f1214]">
                Ajukan Pendaftaran Sidang
            </button>
        </form>
    @endif

    {{-- PROGRES TUGAS AKHIR --}}
    <div class="bg-white rounded-xl border p-6">

        <h3 class="font-bold mb-6">
            Progres Tugas Akhir Saya
        </h3>

        <div class="grid grid-cols-4 text-center gap-2">

            <div>
                <div class="w-10 h-10 mx-auto rounded-full                 {{ $tugasAkhir ? 'bg-green-700 text-white' : 'bg-gray-300 text-black' }} flex items-center justify-center">
                    1
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    Pengajuan Judul
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full {{ $tugasAkhir?->dosen_pembimbing_1 && $tugasAkhir?->dosen_pembimbing_2 ? 'bg-green-700 text-white' : 'bg-gray-300 text-black' }} flex items-center justify-center">
                    2
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    Penetapan Pembimbing
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full {{ $sempro->contains(fn ($item) => $item->status === 'selesai') ? 'bg-green-700 text-white' : 'bg-gray-300 text-black' }} flex items-center justify-center">
                    3
                </div>
                <p class="mt-2 text-sm {{ $sempro->contains(fn ($item) => $item->status === 'selesai') ? 'font-semibold text-green-700' : 'text-gray-600' }}">
                    Seminar Proposal
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full                 {{ $sidang->contains(fn ($item) => in_array($item->status, ['terjadwal', 'selesai'], true)) ? 'bg-green-700 text-white' : 'bg-gray-300 text-black' }} flex items-center justify-center">
                    4
                </div>
                <p class="mt-2 text-sm {{ $sidang->contains(fn ($item) => in_array($item->status, ['terjadwal', 'selesai'], true)) ? 'font-semibold text-green-700' : 'text-gray-600' }}">
                    Sidang
                </p>
            </div>

        </div>
    </div>

    {{-- DAFTAR SIDANG --}}
    <div class="bg-white rounded-xl border p-6">

        <h3 class="text-lg font-bold mb-4">
            Daftar Sidang
        </h3>

        @if($sidang->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">No</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Judul</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Topik</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Status</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600">Jadwal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($sidang as $item)
                            <tr class="border-b last:border-0">
                                <td class="py-3 px-4">{{ $no++ }}</td>
                                <td class="py-3 px-4">{{ $item->tugasAkhir?->judul ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $item->tugasAkhir?->topik ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        {{ in_array($item->status, ['terjadwal', 'selesai'], true) ? 'bg-green-100 text-green-700' : ($item->status === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-500">
                                    {{ $item->waktu?->format('d M Y H:i') ?? 'Menunggu penetapan' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">
                    Belum ada pendaftaran sidang.
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
                    Pilihan Pembimbing I
                </td>
                <td>
                    {{ $tugasAkhir?->calonDosen1?->name ?? '-' }}
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Pilihan Pembimbing II
                </td>
                <td>
                    {{ $tugasAkhir?->calonDosen2?->name ?? '-' }}
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">Dosen Pembimbing I</td>
                <td>{{ $tugasAkhir?->dosenPembimbing1?->name ?? 'Menunggu penetapan' }}</td>
            </tr>
            <tr class="border-b">
                <td class="py-3 font-semibold">Dosen Pembimbing II</td>
                <td>{{ $tugasAkhir?->dosenPembimbing2?->name ?? 'Menunggu penetapan' }}</td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Status
                </td>
                <td>
                    <span class="rounded px-3 py-1 {{ $tugasAkhir?->status === 'Disetujui' ? 'bg-green-100 text-green-700' : ($tugasAkhir?->status === 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $tugasAkhir->status ?? 'Belum Ada' }}
                    </span>
                </td>
            </tr>

            <tr>
                <td class="py-3 font-semibold">
                    Catatan Prodi
                </td>
                <td>
                    {{ $tugasAkhir?->catatan_prodi ?? '-' }}
                </td>
            </tr>

        </table>

    </div>

</div>

@endsection
