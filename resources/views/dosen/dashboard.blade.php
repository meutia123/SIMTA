@extends('layouts.dosen')

@section('title', 'Dashboard Dosen - SIMTA')
@section('page-label', 'Dashboard Dosen')

@section('content')
<div class="space-y-5">

    <div class="bg-[#a51d22] text-white rounded-lg p-6 flex items-center justify-between">
        <div>
            <p class="text-sm">Selamat Datang</p>
            <h1 class="text-2xl font-bold mt-1">Halo, Dosen!</h1>
            <p class="text-xs mt-2 text-red-100">
                Pantau mahasiswa, jadwal seminar dan sidang, serta kelangsungan bimbingan.
            </p>
        </div>

        <a href="#" class="bg-white text-blue-700 font-semibold text-sm px-5 py-3 rounded-lg">
            👥 Lihat Mahasiswa Saya
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                ♟
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $mahasiswaSaya }}</p>
                <p class="text-xs text-gray-500">Mahasiswa Saya</p>
            </div>
        </div>

        <div class="bg-white rounded-lg border p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">
                ▤
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $pengujiSempro }}</p>
                <p class="text-xs text-gray-500">Penguji Sempro</p>
            </div>
        </div>

        <div class="bg-white rounded-lg border p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                ✓
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $pengujiSidang }}</p>
                <p class="text-xs text-gray-500">Penguji Sidang</p>
            </div>
        </div>

        <div class="bg-white rounded-lg border p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center">
                ▣
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $jadwalHariIni }}</p>
                <p class="text-xs text-gray-500">Jadwal Hari Ini</p>
            </div>
        </div>
    </div>

</div>
@endsection