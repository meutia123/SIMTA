
@extends('layouts.app')

@section('title', 'Tugas Akhir')
@section('page-label', 'Tugas Akhir')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center flex-wrap gap-3">
        <h2 class="text-2xl font-bold">
            Tugas Akhir Saya
        </h2>
    </div>

    {{-- NOTIFIKASI BERHASIL --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- NOTIFIKASI VALIDASI --}}
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-lg">
            <p class="font-semibold mb-2">
                Periksa kembali pengajuan kamu:
            </p>

            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- MENU FITUR (URUTAN: AJUKAN JUDUL -> DAFTAR SEMPRO -> DAFTAR SIDANG) --}}
    <div class="bg-white rounded-xl border p-6">

        <h3 class="text-lg font-bold mb-4">
            Fitur Tugas Akhir
        </h3>

        <p class="text-sm text-gray-500 mb-6">
            Ikuti urutan pengajuan tugas akhir dari yang pertama.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <a href="{{ route('tugas-akhir.ajukan-judul') }}"
               class="block bg-green-700 text-white rounded-xl p-5 hover:bg-green-800 transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center font-bold">
                        1
                    </div>
                    <div>
                        <p class="font-semibold">Ajukan Judul</p>
                        <p class="text-xs text-green-100">Pengajuan judul tugas akhir</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('tugas-akhir.daftar-sempro') }}"
               class="block border-2 border-green-700 text-green-700 rounded-xl p-5 hover:bg-green-50 transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full border border-green-700 flex items-center justify-center font-bold text-green-700">
                        2
                    </div>
                    <div>
                        <p class="font-semibold">Daftar Sempro</p>
                        <p class="text-xs text-green-600">Seminar proposal</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('tugas-akhir.daftar-sidang') }}"
               class="block border-2 border-green-700 text-green-700 rounded-xl p-5 hover:bg-green-50 transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full border border-green-700 flex items-center justify-center font-bold text-green-700">
                        3
                    </div>
                    <div>
                        <p class="font-semibold">Daftar Sidang</p>
                        <p class="text-xs text-green-600">Sidang tugas akhir</p>
                    </div>
                </div>
            </a>

        </div>
    </div>

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
                <div class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-black flex items-center justify-center">
                    3
                </div>
                <p class="mt-2 text-sm text-gray-600">
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
