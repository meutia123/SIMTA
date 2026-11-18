
@extends('layouts.tu')

@section('title', 'Administrasi SK - SIMTA')
@section('page-label', 'Administrasi SK')

@section('content')

<div class="space-y-5">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold">Administrasi SK</h1>
            <p class="text-xs text-gray-500 mt-1">
                Kelola permintaan dan penerbitan Surat Keputusan.
            </p>
        </div>

        <input type="text"
               placeholder="Cari mahasiswa..."
               class="bg-white border rounded-lg px-3 py-2 text-xs w-40">
    </div>

    <!-- RINGKASAN -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">Total Permintaan SK</p>
            <h2 class="text-2xl font-bold mt-2">8</h2>
        </div>

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">Perlu Diproses</p>
            <h2 class="text-2xl font-bold text-yellow-600 mt-2">3</h2>
        </div>

        <div class="bg-white rounded-lg border p-4">
            <p class="text-xs text-gray-500">SK Telah Terbit</p>
            <h2 class="text-2xl font-bold text-green-600 mt-2">5</h2>
        </div>

    </div>

    <!-- FILTER -->
    <div class="bg-white border rounded-lg p-4">
        <div class="flex flex-wrap items-center gap-3">

            <label class="text-xs font-semibold">
                Jenis SK:
            </label>

            <select id="filterJenis"
                    class="border rounded-lg px-3 py-2 text-xs">
                <option value="semua">Semua Jenis SK</option>
                <option value="pembimbing">SK Pembimbing</option>
                <option value="penguji">SK Tim Penguji</option>
            </select>

            <select id="filterStatus"
                    class="border rounded-lg px-3 py-2 text-xs">
                <option value="semua">Semua Status</option>
                <option value="perlu">Perlu Diproses</option>
                <option value="terbit">Terbit</option>
            </select>

        </div>
    </div>

    <!-- TABEL -->
    <div class="bg-white border rounded-lg p-4">

        <h2 class="font-bold text-sm mb-4">
            Daftar Administrasi SK
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">

                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Mahasiswa</th>
                        <th class="p-3">Jenis SK</th>
                        <th class="p-3">Tanggal Diminta</th>
                        <th class="p-3">Nomor SK</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>

                <tbody id="tabelSK" class="divide-y">

                    <tr data-jenis="pembimbing" data-status="perlu">
                        <td class="p-3">1</td>
                        <td class="p-3">Difa Febi Ayu</td>
                        <td class="p-3">SK Pembimbing</td>
                        <td class="p-3">25 Mei 2026</td>
                        <td class="p-3">-</td>
                        <td class="p-3">
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded">
                                Perlu Diproses
                            </span>
                        </td>
                        <td class="p-3">
                            <button class="text-blue-600 font-semibold">
                                Proses
                            </button>
                        </td>
                    </tr>

                    <tr data-jenis="pembimbing" data-status="terbit">
                        <td class="p-3">2</td>
                        <td class="p-3">Eva Fitri</td>
                        <td class="p-3">SK Pembimbing</td>
                        <td class="p-3">27 Mei 2026</td>
                        <td class="p-3">SK/012/2026</td>
                        <td class="p-3">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded">
                                Terbit
                            </span>
                        </td>
                        <td class="p-3">
                            <button class="text-blue-600 font-semibold">
                                Unduh
                            </button>
                        </td>
                    </tr>

                    <tr data-jenis="penguji" data-status="perlu">
                        <td class="p-3">3</td>
                        <td class="p-3">Zoria Fitriaca</td>
                        <td class="p-3">SK Tim Penguji</td>
                        <td class="p-3">28 Mei 2026</td>
                        <td class="p-3">-</td>
                        <td class="p-3">
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded">
                                Perlu Diproses
                            </span>
                        </td>
                        <td class="p-3">
                            <button class="text-blue-600 font-semibold">
                                Proses
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection
