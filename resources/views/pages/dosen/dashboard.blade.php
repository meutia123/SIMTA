@extends('layouts.dosen')

@section('title', 'Dashboard Dosen - SIMTA')
@section('page-label', 'Dashboard Dosen')
@section('header-title', 'Dashboard Dosen')
@section('header-subtitle', 'Ringkasan Mahasiswa dan Jadwal Kamu')
@section('content')
<div class="space-y-5">
    <div class="bg-[#a51d22] text-white rounded-lg p-6 flex items-center justify-between">
        <div>
            <p class="text-sm">Selamat Datang</p>
            <h1 class="text-2xl font-bold mt-1">Halo, {{ auth()->user()->name }}!</h1>
            <p class="text-xs mt-2 text-red-100">
                Pantau mahasiswa, jadwal seminar dan sidang, serta kelangsungan bimbingan.
            </p>
        </div>
        <a href="{{ route('dosen.mahasiswa-saya') }}" class="bg-white text-blue-700 font-semibold text-sm px-5 py-3 rounded-lg">
            👥 &nbsp;Lihat Mahasiswa Saya
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">♟</div>
            <div>
                <p class="text-2xl font-bold">{{ $mahasiswaSaya ?? 0 }}</p>
                <p class="text-xs text-gray-500">Mahasiswa Saya</p>
            </div>
        </div>

        <div class="bg-white rounded-lg border p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">▤</div>
            <div>
                <p class="text-2xl font-bold">{{ $pengujiSempro ?? 0 }}</p>
                <p class="text-xs text-gray-500">Penguji Sempro</p>
            </div>
        </div>

        <div class="bg-white rounded-lg border p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">✓</div>
            <div>
                <p class="text-2xl font-bold">{{ $pengujiSidang ?? 0 }}</p>
                <p class="text-xs text-gray-500">Penguji Sidang</p>
            </div>
        </div>

        <div class="bg-white rounded-lg border p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center">▣</div>
            <div>
                <p class="text-2xl font-bold">{{ $jadwalHariIni ?? 0 }}</p>
                <p class="text-xs text-gray-500">Jadwal Hari Ini</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-white border rounded-lg p-5">
            <h2 class="font-bold mb-4">Mahasiswa Terbaru</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-gray-600 border-b">
                            <th class="py-3">No</th>
                            <th>Nama</th>
                            <th>Judul Tugas Akhir</th>
                            <th>Peran</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                       @forelse(($mahasiswaTerbaru ?? collect()) as $index => $tugas)
                       <tr>
                        <td class="py-4">{{ $index + 1 }}</td>
                        <td class="font-medium">{{ $tugas['nama'] ?? '-' }}</td>
                        <td>{{ $tugas['judul'] ?? '-' }}</td>
                        <td>Pembimbing</td>
                        <td>
                            <span class="rounded bg-blue-500 px-2 py-1 text-white">
                            {{ $tugas['status'] ?? '-' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-400">
                        Belum ada mahasiswa bimbingan.
                    </td>
                </tr>
                @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border rounded-lg p-5">
                <h2 class="font-bold mb-4">Jadwal Terdekat</h2>

                <div class="space-y-4 text-xs">
                    <div class="flex gap-3 border-b pb-4">
                        <div class="w-12 h-12 rounded bg-blue-50 text-blue-600 text-center p-1">
                            <b>2</b><br><span>Jun</span>
                        </div>
                        <div>
                            <b>Sempro - Dia Febi Ayu</b>
                            <p class="text-gray-500">09.00 Ruang Seminar 1</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-12 h-12 rounded bg-blue-50 text-blue-600 text-center p-1">
                            <b>20</b><br><span>Jul</span>
                        </div>
                        <div>
                            <b>Sidang - Meutia Sandrina</b>
                            <p class="text-gray-500">09.00 Ruang Sidang</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-5">
                <h2 class="font-bold mb-4">Pengumuman</h2>

                <div class="space-y-4 text-xs">
                    <div class="flex gap-3 border-b pb-4">
                        <span class="text-red-600">◉</span>
                        <div>
                            <b>Jadwal Sidang Tugas Akhir Gelombang 2</b>
                            <p class="text-gray-400 mt-1">15 Mei 2026</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-purple-600">◉</span>
                        <div>
                            <b>Rekap Keberhasilan Dosen Periode Agustus</b>
                            <p class="text-gray-400 mt-1">10 Mei 2026</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection