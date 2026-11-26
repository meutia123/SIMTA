@extends('layouts.dosen')

@section('title', 'Rekap Keterlibatan - SIMTA')
@section('page-label', 'Rekap Keterlibatan')
@section('header-title', 'Rekap Keterlibatan')
@section('header-subtitle', 'Rekap keterlibatan dosen sebagai pembimbing dan penguji')

@section('content')
<div class="space-y-5">
    <div class="rounded-lg border bg-white p-4">
        <label class="block text-xs font-semibold">Periode</label>
        <select class="mt-2 rounded border px-3 py-2 text-sm">
            <option>Wisuda Maret 2026</option>
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-lg border bg-white p-5">
            <p class="text-2xl font-bold">2</p>
            <p class="text-xs text-gray-500">Pembimbing I</p>
        </div>

        <div class="rounded-lg border bg-white p-5">
            <p class="text-2xl font-bold">1</p>
            <p class="text-xs text-gray-500">Pembimbing II</p>
        </div>

        <div class="rounded-lg border bg-white p-5">
            <p class="text-2xl font-bold">2</p>
            <p class="text-xs text-gray-500">Penguji Sempro</p>
        </div>

        <div class="rounded-lg border bg-white p-5">
            <p class="text-2xl font-bold">1</p>
            <p class="text-xs text-gray-500">Penguji Sidang</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border bg-white p-5">
        <h2 class="mb-4 font-bold">Rincian Keterlibatan - Wisuda Maret 2026</h2>

        <table class="w-full text-xs">
            <thead>
                <tr class="border-b bg-gray-50 text-left">
                    <th class="px-3 py-3">No</th>
                    <th class="px-3 py-3">Peran</th>
                    <th class="px-3 py-3">Mahasiswa</th>
                    <th class="px-3 py-3">Tanggal</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($rekap as $index => $item)
                    <tr>
                        <td class="px-3 py-3">{{ $index + 1 }}</td>
                        <td class="px-3 py-3">{{ $item['peran'] }}</td>
                        <td class="px-3 py-3">{{ $item['mahasiswa'] }}</td>
                        <td class="px-3 py-3">{{ $item['tanggal'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection