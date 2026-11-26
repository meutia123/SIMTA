@extends('layouts.tu')

@section('title', 'Dashboard TU - SIMTA')
@section('page-label', 'Dashboard TU!')
@section('page-description', 'Ringkasan permintaan dan penerbitan SK')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="flex flex-col items-start justify-between gap-4 rounded-lg bg-[#841719] p-5 text-white sm:flex-row sm:items-center">
        <div>
            <p class="text-xs text-red-100">Selamat Datang</p>
            <h1 class="mt-1 text-lg font-bold">Halo, {{ Auth::user()->name }}!</h1>
            <p class="mt-1 text-xs text-red-100">
                Kelola administrasi SK dan dokumen tugas akhir mahasiswa.
            </p>
        </div>
        <a href="{{ route('tu.administrasi-sk', ['status' => 'perlu']) }}"
           class="inline-flex items-center gap-3 rounded bg-white px-5 py-2 text-sm font-bold text-blue-700 hover:bg-blue-50">
            <span aria-hidden="true">▤</span>
            Proses SK
        </a>
    </section>

    <a href="{{ route('tu.dokumen') }}" class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white p-4 hover:border-[#841719]">
        <div>
            <p class="font-semibold">Dokumen Tugas Akhir</p>
            <p class="mt-1 text-xs text-gray-500">Unggah, perbarui, dan kelola dokumen mahasiswa.</p>
        </div>
        <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-bold text-blue-700">{{ $documentCount }} dokumen</span>
    </a>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="flex items-center gap-3 rounded-lg border border-gray-300 bg-white p-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-200 text-lg text-indigo-700" aria-hidden="true">▤</span>
            <div>
                <p class="text-2xl font-bold">{{ $statistics['pembimbing_perlu'] }}</p>
                <p class="text-xs text-gray-500">Permintaan SK pembimbing</p>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-lg border border-gray-300 bg-white p-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-200 text-lg text-amber-700" aria-hidden="true">♟</span>
            <div>
                <p class="text-2xl font-bold">{{ $statistics['penguji_perlu'] }}</p>
                <p class="text-xs text-gray-500">Permintaan SK tim penguji</p>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-lg border border-gray-300 bg-white p-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-200 text-lg text-green-700" aria-hidden="true">◷</span>
            <div>
                <p class="text-2xl font-bold">{{ $statistics['pembimbing_terbit'] }}</p>
                <p class="text-xs text-gray-500">SK pembimbing terbit</p>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-lg border border-gray-300 bg-white p-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-200 text-lg text-red-700" aria-hidden="true">✓</span>
            <div>
                <p class="text-2xl font-bold">{{ $statistics['penguji_terbit'] }}</p>
                <p class="text-xs text-gray-500">SK tim penguji terbit</p>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white p-4 xl:col-span-2">
            <h2 class="mb-4 text-sm font-bold">Permintaan SK Terbaru</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
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
                        @forelse($latestRequests as $request)
                            <tr>
                                <td class="p-3">{{ $loop->iteration }}</td>
                                <td class="p-3 font-semibold">{{ $request['mahasiswa'] }}</td>
                                <td class="p-3">{{ $request['jenis_label'] }}</td>
                                <td class="whitespace-nowrap p-3">{{ $request['requested_at']?->locale('id')->translatedFormat('d M Y') ?? '-' }}</td>
                                <td class="p-3">
                                    <span class="inline-flex whitespace-nowrap rounded px-2 py-1 font-semibold {{ $request['status'] === 'terbit' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $request['status'] === 'terbit' ? 'Terbit' : 'Perlu Diproses' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-gray-500">
                                    Belum ada permintaan SK dari tugas akhir atau jadwal yang tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg border border-gray-300 bg-white p-4">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h2 class="text-sm font-bold">Perlu Diproses</h2>
                <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">{{ $statistics['perlu'] }}</span>
            </div>
            <div class="space-y-3">
                @forelse($pendingRequests as $request)
                    <a href="{{ route('tu.proses-sk', ['jenis' => $request['jenis'], 'id' => $request['id']]) }}"
                       class="flex items-center gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-700" aria-hidden="true">▤</span>
                        <span class="min-w-0">
                            <span class="block text-xs font-bold">{{ $request['jenis_label'] }}</span>
                            <span class="mt-1 block truncate text-[10px] text-gray-500">
                                {{ $request['mahasiswa'] }} · {{ $request['requested_at']?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                            </span>
                        </span>
                    </a>
                @empty
                    <p class="py-3 text-xs text-gray-500">Tidak ada SK yang perlu diproses.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
