@extends('layouts.app')

@section('title', 'Dashboard - SIMTA')
@section('page-label', 'Dashboard Mahasiswa')

@section('content')
<div class="space-y-5">

    {{-- Banner Selamat Datang --}}
    <div class="bg-[#841719] text-white rounded-xl p-6">
        <h1 class="text-2xl font-bold">
            Selamat Datang, {{ Auth::user()->name }}!
        </h1>
        <p class="text-sm mt-2 text-red-100">
            Selamat datang di Sistem Informasi Manajemen Tugas Akhir.
            Pantau perkembangan tugas akhir Anda melalui SIMTA.
        </p>
    </div>

    {{-- Pencarian --}}
    <div class="flex items-center gap-3">
        <form action="{{ route('cari') }}" method="GET" role="search" class="flex-1">
            <input
                type="search"
                name="q"
                placeholder="Cari tugas akhir, dokumen, jadwal..."
                class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#841719]"
                required
            >
        </form>

        <a href="{{ route('notifikasi') }}" class="bg-white border border-gray-200 rounded-lg px-4 py-3" aria-label="Notifikasi">
            🔔
        </a>

        <div class="w-10 h-10 rounded-full bg-[#841719] text-white flex items-center justify-center font-bold">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl p-5 border border-gray-200">
            <p class="text-sm text-gray-500">Tugas Akhir</p>
            <h2 class="text-2xl font-bold mt-2" data-testid="tugas-akhir-count">{{ $tugasAkhirCount }}</h2>
            <p class="text-xs text-gray-400 mt-1">Data tugas akhir</p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-200">
            <p class="text-sm text-gray-500">Dokumen</p>
            <h2 class="text-2xl font-bold mt-2" data-testid="dokumen-count">{{ $dokumenCount }}</h2>
            <p class="text-xs text-gray-400 mt-1">Dokumen tersimpan</p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-200">
            <p class="text-sm text-gray-500">Jadwal</p>
            <h2 class="text-2xl font-bold mt-2">{{ $jadwalCount }}</h2>
            <p class="text-xs text-gray-400 mt-1">Jadwal mendatang</p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-200">
            <p class="text-sm text-gray-500">Status Tugas Akhir</p>
            <h2 class="text-lg font-bold mt-2">{{ $tugasAkhir->status ?? 'Belum Ada' }}</h2>
            <p class="text-xs text-gray-400 mt-1">Status pengajuan</p>
        </div>

    </div>

    {{-- Pengumuman --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold">Pengumuman</h2>
        @forelse($pengumuman as $item)
            <a href="{{ route('pengumuman') }}" class="mt-3 block border-b border-gray-100 py-3 last:border-0">
                <p class="font-semibold">{{ $item->judul }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($item->isi, 140) }}</p>
            </a>
        @empty
            <p class="mt-2 text-sm text-gray-500">Belum ada pengumuman.</p>
        @endforelse
        <a href="{{ route('pengumuman') }}" class="mt-4 inline-block text-sm font-semibold text-[#841719] hover:underline">
            Lihat semua pengumuman
        </a>
    </div>

</div>
@endsection