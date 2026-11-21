@extends('layouts.tu')

@section('title', 'Proses SK - SIMTA')
@section('page-label', 'Proses SK')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-xl font-bold">Proses SK</h1>
        <p class="text-sm text-gray-500">
            Input nomor dan tanggal SK
        </p>
    </div>

    <!-- Tombol Kembali -->
    <div>
        <a href="{{ route('tu.administrasi-sk') }}"
            class="inline-flex items-center px-4 py-2 bg-white border rounded-lg shadow-sm hover:bg-gray-100">
            ← Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form kiri -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">

            <h2 class="font-semibold text-lg">
                Dokumen SK - Elva Fitri
            </h2>

            <p class="text-sm text-gray-500 mb-6">
                Diminta pada 12 Jan 2026
            </p>

            <!-- Nomor SK -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">
                    Nomor SK
                </label>

                <input
                    type="text"
                    placeholder="Contoh: 123/Un.13/2026"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

            <!-- Jenis SK -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">
                    Jenis SK
                </label>

                <select class="w-full border rounded-lg px-3 py-2">
                    <option>SK Pembimbing</option>
                    <option>SK Tim Penguji</option>
                </select>
            </div>

            <!-- Tanggal -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">
                    Tanggal SK
                </label>

                <input
                    type="date"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

            <!-- Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">
                    Unggah File SK
                </label>

                <input
                    type="file"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

            <!-- Tombol -->
            <button
                class="bg-red-700 hover:bg-red-800 text-white px-5 py-2 rounded-lg">
                Simpan dan Terbitkan
            </button>

        </div>

        <!-- Informasi kanan -->
        <div>

            <div class="bg-purple-100 text-purple-700 p-4 rounded-xl text-sm">
                Setelah diterbitkan, SK dapat diakses mahasiswa dan dosen terkait.
            </div>

        </div>

    </div>

</div>

@endsection