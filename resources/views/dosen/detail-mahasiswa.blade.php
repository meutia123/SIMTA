@extends('layouts.dosen')

@section('title', 'Detail Mahasiswa - SIMTA')
@section('page-label', 'Detail Mahasiswa')
@section('page-description', 'Informasi administrasi tugas akhir')

@section('content')
<div class="space-y-5">
    <a href="{{ route('dosen.mahasiswa') }}" class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold">
        ← &nbsp; Kembali
    </a>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h1 class="font-bold">Progres Tugas Akhir</h1>
        <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-4">
            @foreach($steps as $index => $step)
                <div class="relative text-center">
                    @if($index > 0)
                        <div class="absolute right-1/2 top-4 -z-0 hidden h-0.5 w-full {{ $steps[$index - 1]['done'] ? 'bg-blue-600' : 'bg-gray-200' }} md:block"></div>
                    @endif
                    <span class="relative z-10 mx-auto flex h-8 w-8 items-center justify-center rounded-full {{ $step['done'] ? 'bg-blue-700 text-white' : 'border border-gray-300 bg-white text-gray-500' }}">
                        {{ $step['done'] ? '✓' : $index + 1 }}
                    </span>
                    <span class="mt-2 block text-xs font-semibold {{ $step['done'] ? 'text-gray-900' : 'text-gray-500' }}">{{ $step['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.7fr)_minmax(260px,0.9fr)]">
        <section class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="font-bold">Data Mahasiswa</h2>
            <dl class="mt-4 divide-y divide-gray-200 text-sm">
                <div class="grid grid-cols-[120px_1fr] gap-3 py-3"><dt class="font-semibold">Nama</dt><dd>{{ $tugasAkhir->mahasiswa->name }}</dd></div>
                <div class="grid grid-cols-[120px_1fr] gap-3 py-3"><dt class="font-semibold">NIM</dt><dd>{{ $tugasAkhir->mahasiswa->nim ?: '-' }}</dd></div>
                <div class="grid grid-cols-[120px_1fr] gap-3 py-3"><dt class="font-semibold">Program Studi</dt><dd>{{ $tugasAkhir->mahasiswa->program_studi ?: 'Sistem Informasi' }}</dd></div>
                <div class="grid grid-cols-[120px_1fr] gap-3 py-3"><dt class="font-semibold">Judul</dt><dd>{{ $tugasAkhir->judul }}</dd></div>
                <div class="grid grid-cols-[120px_1fr] gap-3 py-3"><dt class="font-semibold">Peran Saya</dt><dd>{{ implode(', ', $roles) }}</dd></div>
                <div class="grid grid-cols-[120px_1fr] gap-3 py-3"><dt class="font-semibold">Status</dt><dd>{{ $tugasAkhir->status }}</dd></div>
            </dl>
        </section>

        <div class="space-y-5">
            <section class="rounded-xl border border-gray-200 bg-white p-5">
                <h2 class="font-bold">Dosen Pembimbing</h2>
                <div class="mt-3 divide-y divide-gray-100 text-sm">
                    <div class="flex items-center gap-3 py-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-700 font-semibold text-white">I</span>
                        <div>
                            <p class="font-semibold">Pembimbing I</p>
                            <p class="text-xs text-gray-500">
                                {{ (int) $tugasAkhir->dosen_pembimbing_1 === (int) Auth::id() ? 'Kamu' : ($tugasAkhir->dosenPembimbing1?->name ?? 'Belum ditetapkan') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 py-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-700 font-semibold text-white">II</span>
                        <div>
                            <p class="font-semibold">Pembimbing II</p>
                            <p class="text-xs text-gray-500">
                                {{ (int) $tugasAkhir->dosen_pembimbing_2 === (int) Auth::id() ? 'Kamu' : ($tugasAkhir->dosenPembimbing2?->name ?? 'Belum ditetapkan') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            @if($suratKeputusan->isNotEmpty())
                <section class="rounded-xl border border-gray-200 bg-white p-5">
                    <h2 class="font-bold">SK Terkait</h2>
                    <div class="mt-3 space-y-3">
                        @foreach($suratKeputusan as $surat)
                            <div>
                                <p class="mb-2 text-sm">{{ $surat['label'] }}</p>
                                <a href="{{ route('surat-keputusan.download', ['jenis' => $surat['jenis'], 'id' => $surat['id']]) }}"
                                   class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold hover:bg-gray-50">
                                    Unduh SK
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
@endsection
