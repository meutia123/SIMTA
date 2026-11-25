@extends('layouts.prodi')

@section('title', 'Dashboard Prodi - SIMTA')
@section('page-label', 'Dashboard Prodi')

@section('content')

<div class="space-y-5">

    <!-- HEADER -->
    <div class="bg-white rounded-lg border border-gray-200
                p-4 flex items-center justify-between gap-3">

        <div>
            <h1 class="font-bold text-lg">Dashboard Prodi!</h1>
            <p class="text-xs text-gray-500">
                Ringkasan mahasiswa, dosen, dan tugas akhir
            </p>
        </div>

        <div class="flex items-center gap-3">
            <input type="text"
                   placeholder="Cari..."
                   class="hidden sm:block bg-gray-100 rounded px-4 py-2
                          text-xs w-40">

            <div class="w-8 h-8 rounded-full bg-gray-200
                        flex items-center justify-center text-sm font-bold">
                P
            </div>
        </div>
    </div>

    <!-- BANNER SELAMAT DATANG -->
    <div class="bg-green-700 text-white rounded-lg p-5
                flex items-center justify-between gap-4">

        <div>
            <h2 class="font-bold text-lg">Halo, Prodi!</h2>
            <p class="text-xs text-green-100 mt-1">
                Kelola data mahasiswa, dosen, dan pantau kemajuan tugas akhir.
            </p>
        </div>

        <a href="{{ route('prodi.pengajuan-tugas-akhir') }}" class="bg-white text-green-700 rounded px-4 py-2
                   text-xs font-bold whitespace-nowrap hover:bg-green-50">
            Review Pengajuan
        </a>
    </div>

    <!-- STATISTIK -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">
                Total Mahasiswa
            </p>
            <h2 class="text-2xl font-bold mt-2">150</h2>
        </div>

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">
                Total Dosen
            </p>
            <h2 class="text-2xl font-bold mt-2">25</h2>
        </div>

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">
                Tugas Akhir Aktif
            </p>
            <h2 class="text-2xl font-bold mt-2">45</h2>
        </div>

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">
                Selesai Tahun Ini
            </p>
            <h2 class="text-2xl font-bold mt-2">12</h2>
        </div>

    </div>

    <!-- TABEL MAHASISWA TERBARU -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">

        <h3 class="font-bold text-sm mb-4">
            Mahasiswa Tugas Akhir Terbaru
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">

                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">NIM</th>
                        <th class="p-3">Nama Mahasiswa</th>
                        <th class="p-3">Judul</th>
                        <th class="p-3">Pembimbing</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    <tr>
                        <td class="p-3">1</td>
                        <td class="p-3">2021001</td>
                        <td class="p-3">Difa Febi Ayu</td>
                        <td class="p-3">Sistem Informasi...</td>
                        <td class="p-3">Dr. Budi Santoso</td>
                        <td class="p-3">
                            <span class="bg-blue-100 text-blue-700
                                         px-2 py-1 rounded">
                                Proses
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td class="p-3">2</td>
                        <td class="p-3">2021002</td>
                        <td class="p-3">Eva Fitri</td>
                        <td class="p-3">Aplikasi Mobile...</td>
                        <td class="p-3">Dr. Siti Rahayu</td>
                        <td class="p-3">
                            <span class="bg-yellow-100 text-yellow-700
                                         px-2 py-1 rounded">
                                Sempro
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td class="p-3">3</td>
                        <td class="p-3">2021003</td>
                        <td class="p-3">Meutis Sandrina</td>
                        <td class="p-3">Analisis Data...</td>
                        <td class="p-3">Dr. Ahmad Fauzi</td>
                        <td class="p-3">
                            <span class="bg-green-100 text-green-700
                                         px-2 py-1 rounded">
                                Sidang
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td class="p-3">4</td>
                        <td class="p-3">2021004</td>
                        <td class="p-3">Zoria Fitriaca</td>
                        <td class="p-3">Machine Learning...</td>
                        <td class="p-3">Dr. Rina Marlina</td>
                        <td class="p-3">
                            <span class="bg-gray-100 text-gray-700
                                         px-2 py-1 rounded">
                                Menunggu
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection