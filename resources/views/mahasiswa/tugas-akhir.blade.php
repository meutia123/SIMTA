@extends('layouts.app')

@section('title', 'Tugas Akhir')
@section('page-label', 'Tugas Akhir')

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">
            Tugas Akhir Saya
        </h2>

        <div class="flex gap-2">
            <button class="bg-green-700 text-white px-4 py-2 rounded">
                Ajukan Judul
            </button>

            <button class="bg-green-700 text-white px-4 py-2 rounded">
                Daftar Sempro
            </button>

            <button class="bg-green-700 text-white px-4 py-2 rounded">
                Daftar Sidang
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h3 class="font-bold mb-6">
            Progres Tugas Akhir Saya
        </h3>

        <div class="grid grid-cols-4 text-center">

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-red-700 text-white flex items-center justify-center">
                    1
                </div>
                <p class="mt-2 text-sm">
                    Pengajuan Judul
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-green-700 text-white flex items-center justify-center">
                    2
                </div>
                <p class="mt-2 text-sm">
                    Penetapan Pembimbing
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-black flex items-center justify-center">
                    3
                </div>
                <p class="mt-2 text-sm">
                    Seminar Proposal
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-black flex items-center justify-center">
                    4
                </div>
                <p class="mt-2 text-sm">
                    Sidang
                </p>
            </div>

        </div>
    </div>

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