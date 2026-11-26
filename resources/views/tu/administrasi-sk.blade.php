@extends('layouts.tu')

@section('title', 'Administrasi SK - SIMTA')
@section('page-label', 'Administrasi SK')
@section('page-description', 'Permintaan dan penerbitan SK')

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

    <section class="flex flex-wrap items-center gap-3">
        <label for="filterJenis" class="text-sm font-semibold">Jenis SK</label>
        <form action="{{ route('tu.administrasi-sk') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <select id="filterJenis" name="jenis" class="min-w-40 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
                <option value="semua" @selected($filters['jenis'] === 'semua')>Semua</option>
                <option value="pembimbing" @selected($filters['jenis'] === 'pembimbing')>SK Pembimbing</option>
                <option value="penguji" @selected($filters['jenis'] === 'penguji')>SK Tim Penguji</option>
            </select>
            @if($filters['status'] !== 'semua')
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endif
            @if($filters['q'])
                <input type="hidden" name="q" value="{{ $filters['q'] }}">
            @endif
            <button type="submit" class="rounded-lg bg-[#841719] px-4 py-2 text-sm font-semibold text-white hover:bg-[#6f1214]">
                Tampilkan
            </button>
        </form>
    </section>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <a href="{{ route('tu.administrasi-sk', ['status' => 'perlu', 'jenis' => $filters['jenis'], 'q' => $filters['q']]) }}"
           class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-4 hover:border-amber-300">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-lg text-red-700" aria-hidden="true">▤</span>
            <span>
                <span class="block text-2xl font-bold">{{ $statistics['perlu'] }}</span>
                <span class="block text-xs text-gray-500">Perlu Diproses</span>
            </span>
        </a>
        <a href="{{ route('tu.administrasi-sk', ['status' => 'terbit', 'jenis' => $filters['jenis'], 'q' => $filters['q']]) }}"
           class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-4 hover:border-green-300">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-lg text-green-700" aria-hidden="true">◷</span>
            <span>
                <span class="block text-2xl font-bold">{{ $statistics['terbit'] }}</span>
                <span class="block text-xs text-gray-500">Sudah Terbit</span>
            </span>
        </a>
    </section>

    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white p-4">
        <h2 class="mb-4 text-sm font-bold">Daftar Administrasi SK</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Mahasiswa</th>
                        <th class="p-3">Jenis SK</th>
                        <th class="whitespace-nowrap p-3">Diminta</th>
                        <th class="p-3">Nomor SK</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($requests as $request)
                        <tr>
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3 font-semibold">{{ $request['mahasiswa'] }}</td>
                            <td class="p-3">{{ $request['jenis_label'] }}</td>
                            <td class="whitespace-nowrap p-3">{{ $request['requested_at']?->locale('id')->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap p-3">{{ $request['nomor_sk'] ?: '-' }}</td>
                            <td class="p-3">
                                <span class="inline-flex whitespace-nowrap rounded px-2 py-1 font-semibold {{ $request['status'] === 'terbit' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $request['status'] === 'terbit' ? 'Terbit' : 'Perlu Diproses' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap p-3">
                                @if($request['status'] === 'terbit')
                                    <a href="{{ route('surat-keputusan.download', ['jenis' => $request['jenis'], 'id' => $request['id']]) }}"
                                       class="rounded bg-white px-3 py-1.5 font-semibold text-gray-800 ring-1 ring-gray-300 hover:bg-gray-50">
                                        Unduh
                                    </a>
                                @else
                                    <a href="{{ route('tu.proses-sk', ['jenis' => $request['jenis'], 'id' => $request['id']]) }}"
                                       class="rounded bg-blue-600 px-3 py-1.5 font-semibold text-white hover:bg-blue-700">
                                        Proses SK
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                Tidak ada permintaan SK yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($filters['status'] !== 'semua')
        <a href="{{ route('tu.administrasi-sk', ['jenis' => $filters['jenis'], 'q' => $filters['q']]) }}"
           class="inline-block text-sm font-semibold text-[#841719] hover:underline">
            Hapus filter status
        </a>
    @endif
</div>
@endsection
