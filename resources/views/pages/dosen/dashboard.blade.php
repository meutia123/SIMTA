@extends('layouts.dosen')

@section('title', 'Dashboard Dosen - SIMTA')
@section('page-label', 'Dashboard Dosen')
@section('header-title', 'Dashboard Dosen')
@section('header-subtitle', 'Ringkasan Mahasiswa dan Jadwal Kamu')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between rounded-lg bg-[#a51d22] p-6 text-white">
        <div>
            <p class="text-sm">Selamat Datang</p>
            <h1 class="mt-1 text-2xl font-bold">Halo, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-xs text-red-100">
                Pantau mahasiswa, jadwal seminar dan sidang, serta kelangsungan bimbingan.
            </p>
        </div>

        <a href="{{ route('dosen.mahasiswa-saya') }}"
           class="rounded-lg bg-white px-5 py-3 text-sm font-semibold text-blue-700">
            👥 &nbsp;Lihat Mahasiswa Saya
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="flex items-center gap-4 rounded-lg border bg-white p-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600">♟</div>
            <div>
                <p class="text-2xl font-bold">{{ $mahasiswaSaya ?? 0 }}</p>
                <p class="text-xs text-gray-500">Mahasiswa Saya</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-lg border bg-white p-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600">▤</div>
            <div>
                <p class="text-2xl font-bold">{{ $pengujiSempro ?? 0 }}</p>
                <p class="text-xs text-gray-500">Penguji Sempro</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-lg border bg-white p-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600">✓</div>
            <div>
                <p class="text-2xl font-bold">{{ $pengujiSidang ?? 0 }}</p>
                <p class="text-xs text-gray-500">Penguji Sidang</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-lg border bg-white p-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600">▣</div>
            <div>
                <p class="text-2xl font-bold">{{ $jadwalHariIni ?? 0 }}</p>
                <p class="text-xs text-gray-500">Jadwal Hari Ini</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-lg border bg-white p-5 xl:col-span-2">
            <h2 class="mb-4 font-bold">Mahasiswa Terbaru</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b text-left text-gray-600">
                            <th class="py-3">No</th>
                            <th>Nama</th>
                            <th>Judul Tugas Akhir</th>
                            <th>Peran</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse(($mahasiswaTerbaru ?? collect()) as $index => $mahasiswa)
                            @php
                                $status = data_get($mahasiswa, 'status', '-');
                                $statusLower = strtolower((string) $status);
                            @endphp

                            <tr>
                                <td class="py-4">{{ $index + 1 }}</td>
                                <td class="font-medium">{{ data_get($mahasiswa, 'nama', '-') }}</td>
                                <td>{{ data_get($mahasiswa, 'judul', '-') }}</td>
                                <td>Pembimbing</td>
                                <td>
                                    <span class="rounded px-2 py-1 text-white
                                        {{ $statusLower === 'sidang'
                                            ? 'bg-green-500'
                                            : ($statusLower === 'sempro'
                                                ? 'bg-yellow-500'
                                                : 'bg-blue-500') }}">
                                        {{ $status }}
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
            <div class="rounded-lg border bg-white p-5">
                <h2 class="mb-4 font-bold">Jadwal Terdekat</h2>

                <div class="space-y-4 text-xs">
                    <div class="flex gap-3 border-b pb-4">
                        <div class="h-12 w-12 rounded bg-blue-50 p-1 text-center text-blue-600">
                            <b>2</b><br><span>Jun</span>
                        </div>
                        <div>
                            <b>Sempro - Dia Febi Ayu</b>
                            <p class="text-gray-500">09.00 Ruang Seminar 1</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="h-12 w-12 rounded bg-blue-50 p-1 text-center text-blue-600">
                            <b>20</b><br><span>Jul</span>
                        </div>
                        <div>
                            <b>Sidang - Meutia Sandrina</b>
                            <p class="text-gray-500">09.00 Ruang Sidang</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border bg-white p-5">
                <h2 class="mb-4 font-bold">Pengumuman</h2>

                <div class="space-y-4 text-xs">
                    <div class="flex gap-3 border-b pb-4">
                        <span class="text-red-600">◉</span>
                        <div>
                            <b>Jadwal Sidang Tugas Akhir Gelombang 2</b>
                            <p class="mt-1 text-gray-400">15 Mei 2026</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-purple-600">◉</span>
                        <div>
                            <b>Rekap Keberhasilan Dosen Periode Agustus</b>
                            <p class="mt-1 text-gray-400">10 Mei 2026</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection