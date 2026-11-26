@extends('layouts.dosen')

@section('title', 'SK & Dokumen - SIMTA')
@section('page-label', 'SK & Dokumen')
@section('header-title', 'SK & Dokumen')
@section('header-subtitle', 'SK yang berkaitan dengan dosen')

@section('content')
<div class="space-y-5">
    <div class="flex gap-3">
        <button class="rounded-full bg-[#a51d22] px-6 py-2 text-xs font-semibold text-white">
            Semua
        </button>
        <button class="rounded-full border bg-white px-6 py-2 text-xs font-semibold">
            SK Pembimbing
        </button>
        <button class="rounded-full border bg-white px-6 py-2 text-xs font-semibold">
            SK Tim Penguji
        </button>
    </div>

    <div class="overflow-x-auto rounded-lg border bg-white p-5">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-3">No</th>
                    <th>Jenis SK</th>
                    <th>Mahasiswa</th>
                    <th>Nomor SK</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse(($skDokumen ?? []) as $index => $item)
                    <tr>
                        <td class="py-4">{{ $index + 1 }}</td>
                        <td>{{ $item['jenis'] }}</td>
                        <td>{{ $item['mahasiswa'] }}</td>
                        <td>{{ $item['nomor'] }}</td>
                        <td>{{ $item['tanggal'] }}</td>
                        <td>
                            <span class="rounded-full border border-green-300 bg-green-50 px-3 py-1 text-green-700">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td>
                            <button class="rounded-full border px-4 py-1">Unduh</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            Belum ada SK atau dokumen.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection