@extends('layouts.dosen')

@section('title', 'Detail Mahasiswa - SIMTA')
@section('page-label', 'Detail Mahasiswa')
@section('header-title', 'Detail Mahasiswa')
@section('header-subtitle', 'Informasi administrasi tugas akhir')

@section('content')
@php
    $tahapan = [
        'Pengajuan Judul',
        'Penetapan Pembimbing',
        'Sempro',
        'Sidang/Komprehensif',
    ];

    $statusClass = match ($mahasiswa['status']) {
        'Sempro' => 'bg-yellow-400 text-yellow-800',
        'Sidang' => 'bg-green-400 text-green-900',
        'Pembimbing Ditentukan' => 'bg-indigo-400 text-indigo-950',
        default => 'bg-gray-400 text-white',
    };
@endphp

<div class="space-y-5">
    <a href="{{ route('dosen.mahasiswa-saya') }}"
       class="inline-flex items-center gap-3 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm hover:bg-gray-50">
        <span aria-hidden="true">←</span>
        <span>Kembali</span>
    </a>

    {{-- Progres tugas akhir --}}
    <section class="rounded-lg bg-white p-5 shadow-sm md:p-7">
        <h2 class="mb-6 font-bold">Progres Tugas Akhir</h2>

        <div class="grid grid-cols-2 gap-5 md:grid-cols-4 md:gap-2">
            @foreach ($tahapan as $index => $tahap)
                @php
                    $selesai = ($index + 1) <= $mahasiswa['tahap'];
                @endphp

                <div class="relative flex flex-col items-center text-center">
                    @if ($index < count($tahapan) - 1)
                        <div class="absolute left-1/2 top-3 hidden h-0.5 w-full md:block
                            {{ $index + 1 < $mahasiswa['tahap'] ? 'bg-blue-700' : 'bg-gray-300' }}">
                        </div>
                    @endif

                    <div class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full border text-xs
                        {{ $selesai ? 'border-blue-700 bg-blue-700 text-white' : 'border-gray-300 bg-white text-gray-500' }}">
                        {{ $selesai ? '✓' : $index + 1 }}
                    </div>

                    <p class="mt-2 text-xs font-semibold
                        {{ $selesai ? 'text-gray-900' : 'text-gray-400' }}">
                        {{ $tahap }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Data dan informasi pembimbing --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <section class="rounded-lg bg-white p-5 shadow-sm md:p-7 lg:col-span-2">
            <h2 class="mb-4 font-bold">Data Mahasiswa</h2>

            <dl class="divide-y divide-gray-300 text-sm">
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-[120px_1fr] sm:gap-4">
                    <dt class="font-semibold text-gray-600">Nama</dt>
                    <dd>{{ $mahasiswa['nama'] }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-[120px_1fr] sm:gap-4">
                    <dt class="font-semibold text-gray-600">NIM</dt>
                    <dd>{{ $mahasiswa['nim'] }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-[120px_1fr] sm:gap-4">
                    <dt class="font-semibold text-gray-600">Program Studi</dt>
                    <dd>{{ $mahasiswa['program_studi'] }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-[120px_1fr] sm:gap-4">
                    <dt class="font-semibold text-gray-600">Judul</dt>
                    <dd>{{ $mahasiswa['judul'] }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-[120px_1fr] sm:gap-4">
                    <dt class="font-semibold text-gray-600">Peran Saya</dt>
                    <dd>{{ $mahasiswa['peran'] }}</dd>
                </div>

                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-[120px_1fr] sm:gap-4">
                    <dt class="font-semibold text-gray-600">Status</dt>
                    <dd>
                        <span class="{{ $statusClass }} inline-block rounded px-3 py-1 text-xs font-semibold">
                            {{ $mahasiswa['status'] }}
                        </span>
                    </dd>
                </div>
            </dl>
        </section>

        <div class="space-y-5">
            <section class="rounded-lg bg-white p-5 shadow-sm md:p-7">
                <h2 class="mb-4 font-bold">Dosen Pembimbing</h2>

                <div class="divide-y divide-gray-300">
                    <div class="flex items-center gap-3 py-3">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-700 text-xs font-bold text-white">
                            I
                        </span>
                        <div>
                            <p class="text-sm font-semibold">Pembimbing I</p>
                            <p class="text-xs text-gray-500">{{ $mahasiswa['pembimbing_1'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 py-3">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-700 text-xs font-bold text-white">
                            II
                        </span>
                        <div>
                            <p class="text-sm font-semibold">Pembimbing II</p>
                            <p class="text-xs text-gray-500">{{ $mahasiswa['pembimbing_2'] }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-lg bg-white p-5 shadow-sm md:p-7">
                <h2 class="mb-4 font-bold">SK Pembimbing</h2>

                {{-- Ganti # dengan URL/path file SK bila dokumennya sudah tersedia --}}
                <a href="#"
                   class="block rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-semibold hover:bg-gray-50">
                    Unduh SK
                </a>
            </section>
        </div>
    </div>
</div>
@endsection