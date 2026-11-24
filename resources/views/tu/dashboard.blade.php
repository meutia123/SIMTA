
@extends('layouts.tu')

@section('title', 'Dashboard TU - SIMTA')
@section('page-label', 'Dashboard TU')

@section('content')

<div class="space-y-5">

    <!-- HEADER -->
    <div class="bg-white rounded-lg border border-gray-200
                p-4 flex items-center justify-between gap-3">

        <div>
            <h1 class="font-bold text-lg">Dashboard TU!</h1>
            <p class="text-xs text-gray-500">
                Ringkasan permintaan dan penerbitan SK
            </p>
        </div>

        <div class="flex items-center gap-3">
            <input type="text"
                   placeholder="Cari..."
                   class="hidden sm:block bg-gray-100 rounded px-4 py-2
                          text-xs w-40">

            <div class="w-8 h-8 rounded-full bg-gray-200
                        flex items-center justify-center text-sm font-bold">
                T
            </div>
        </div>
    </div>

    <!-- BANNER SELAMAT DATANG -->
    <div class="bg-blue-700 text-white rounded-lg p-5
                flex items-center justify-between gap-4">

        <div>
            <h2 class="font-bold text-lg">Halo, TU!</h2>
            <p class="text-xs text-blue-100 mt-1">
                Periksa permintaan SK pembimbing dan SK Tim Penguji
                dari mahasiswa.
            </p>
        </div>

        <a href="{{ route('tu.administrasi-sk') }}"
           class="bg-white text-blue-700 rounded px-4 py-2
                  text-xs font-bold whitespace-nowrap hover:bg-blue-50">
            Proses SK
        </a>
    </div>

    <!-- STATISTIK -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">
                Permintaan SK Pembimbing
            </p>
            <h2 class="text-2xl font-bold mt-2">2</h2>
        </div>

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">
                Permintaan SK Tim Penguji
            </p>
            <h2 class="text-2xl font-bold mt-2">2</h2>
        </div>

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">
                SK Pembimbing Terbit
            </p>
            <h2 class="text-2xl font-bold mt-2">2</h2>
        </div>

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">
                SK Tim Penguji Terbit
            </p>
            <h2 class="text-2xl font-bold mt-2">2</h2>
        </div>

    </div>

    <!-- TABEL DAN PERLU DIPROSES -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        <!-- PERMINTAAN SK TERBARU -->
        <div class="xl:col-span-2 bg-white rounded-lg
                    border border-gray-200 p-4">

            <h3 class="font-bold text-sm mb-4">
                Permintaan SK Terbaru
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">

                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-3">No</th>
                            <th class="p-3">Mahasiswa</th>
                            <th class="p-3">Jenis SK</th>
                            <th class="p-3">Diminta</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        <tr>
                            <td class="p-3">1</td>
                            <td class="p-3">Difa Febi Ayu</td>
                            <td class="p-3">SK Pembimbing</td>
                            <td class="p-3">25 Mei 2026</td>
                            <td class="p-3">
                                <span class="bg-yellow-100 text-yellow-700
                                             px-2 py-1 rounded">
                                    Perlu Diproses
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td class="p-3">2</td>
                            <td class="p-3">Eva Fitri</td>
                            <td class="p-3">SK Pembimbing</td>
                            <td class="p-3">27 Mei 2026</td>
                            <td class="p-3">
                                <span class="bg-green-100 text-green-700
                                             px-2 py-1 rounded">
                                    Terbit
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td class="p-3">3</td>
                            <td class="p-3">Meutis Sandrina</td>
                            <td class="p-3">SK Pembimbing</td>
                            <td class="p-3">12 Jan 2026</td>
                            <td class="p-3">
                                <span class="bg-yellow-100 text-yellow-700
                                             px-2 py-1 rounded">
                                    Perlu Diproses
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td class="p-3">4</td>
                            <td class="p-3">Zoria Fitriaca</td>
                            <td class="p-3">SK Tim Penguji</td>
                            <td class="p-3">28 Mei 2026</td>
                            <td class="p-3">
                                <span class="bg-yellow-100 text-yellow-700
                                             px-2 py-1 rounded">
                                    Perlu Diproses
                                </span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- PERLU DIPROSES -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">

            <h3 class="font-bold text-sm mb-4">
                Perlu Diproses
            </h3>

            <div class="space-y-3">

                <div class="flex items-center gap-3 border-b pb-3">
                    <div class="bg-red-100 text-red-600 rounded p-3">
                        SK
                    </div>
                    <div>
                        <p class="text-xs font-bold">SK Pembimbing</p>
                        <p class="text-[10px] text-gray-500">
                            Eva Fitri · 27 Mei 2026
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-b pb-3">
                    <div class="bg-red-100 text-red-600 rounded p-3">
                        SK
                    </div>
                    <div>
                        <p class="text-xs font-bold">SK Tim Penguji</p>
                        <p class="text-[10px] text-gray-500">
                            Zoria Fitriaca · 28 Mei 2026
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="bg-red-100 text-red-600 rounded p-3">
                        SK
                    </div>
                    <div>
                        <p class="text-xs font-bold">SK Tim Penguji</p>
                        <p class="text-[10px] text-gray-500">
                            Fajar Ramadhan · 29 Mei 2026
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

@endsection
