
@extends('layouts.app')

@section('title', 'Tugas Akhir')
@section('page-label', 'Tugas Akhir')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center flex-wrap gap-3">
        <h2 class="text-2xl font-bold">
            Tugas Akhir Saya
        </h2>

        <div class="flex gap-2 flex-wrap">
            <a href="#form-ajukan"
               class="bg-green-700 text-white px-4 py-2 rounded">
                Ajukan Judul
            </a>

            <button type="button"
                    class="bg-green-700 text-white px-4 py-2 rounded">
                Daftar Sempro
            </button>

            <button type="button"
                    class="bg-green-700 text-white px-4 py-2 rounded">
                Daftar Sidang
            </button>
        </div>
    </div>

    {{-- NOTIFIKASI BERHASIL --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- NOTIFIKASI VALIDASI --}}
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-lg">
            <p class="font-semibold mb-2">
                Periksa kembali pengajuan kamu:
            </p>

            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM PENGAJUAN JUDUL --}}
    <div id="form-ajukan" class="bg-white rounded-xl border p-6">

        <h3 class="text-lg font-bold mb-4">
            Form Pengajuan Judul Tugas Akhir
        </h3>

        <form action="{{ route('tugas-akhir.store') }}" method="POST">
            @csrf

            {{-- JUDUL --}}
            <div class="mb-4">
                <label class="block font-semibold mb-2">
                    Judul Tugas Akhir
                </label>

                <input
                    type="text"
                    name="judul"
                    value="{{ old('judul') }}"
                    placeholder="Masukkan judul tugas akhir"
                    class="w-full border rounded-lg p-3"
                    maxlength="255"
                    required
                >
            </div>

            {{-- TOPIK --}}
            <div class="mb-4">
                <label class="block font-semibold mb-2">
                    Topik Tugas Akhir
                </label>

                <input
                    type="text"
                    name="topik"
                    value="{{ old('topik') }}"
                    placeholder="Masukkan topik tugas akhir"
                    class="w-full border rounded-lg p-3"
                    maxlength="255"
                    required
                >
            </div>

            {{-- DESKRIPSI --}}
            <div class="mb-4">
                <label class="block font-semibold mb-2">
                    Deskripsi
                </label>

                <textarea
                    name="deskripsi"
                    rows="4"
                    placeholder="Masukkan deskripsi tugas akhir"
                    class="w-full border rounded-lg p-3"
                >{{ old('deskripsi') }}</textarea>
            </div>

            {{-- TIGA CALON DOSEN --}}
            @for($i = 1; $i <= 3; $i++)
                <div class="mb-4">
                    <label class="block font-semibold mb-2">
                        Calon Dosen Pembimbing {{ $i }}
                    </label>

                    <select
                        name="calon_dosen_{{ $i }}"
                        class="w-full border rounded-lg p-3"
                        required
                    >
                        <option value="">-- Pilih Dosen --</option>

                        @foreach($dosen as $item)
                            <option
                                value="{{ $item->id }}"
                                {{ old('calon_dosen_'.$i) == $item->id ? 'selected' : '' }}
                            >
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endfor

            {{-- TOMBOL KIRIM --}}
            <button
                type="submit"
                class="bg-green-700 hover:bg-green-800 text-white px-5 py-2 rounded-lg"
            >
                Kirim Pengajuan
            </button>

        </form>

    </div>

    {{-- PROGRES TUGAS AKHIR --}}
    <div class="bg-white rounded-xl border p-6">

        <h3 class="font-bold mb-6">
            Progres Tugas Akhir Saya
        </h3>

        <div class="grid grid-cols-4 text-center gap-2">

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-red-700 text-white flex items-center justify-center">
                    1
                </div>
                <p class="mt-2 text-sm">
                    Pengajuan Judul
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-green-700 text-white flex items-center justify-center">
                    2
                </div>
                <p class="mt-2 text-sm">
                    Penetapan Pembimbing
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-black flex items-center justify-center">
                    3
                </div>
                <p class="mt-2 text-sm">
                    Seminar Proposal
                </p>
            </div>

            <div>
                <div class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-black flex items-center justify-center">
                    4
                </div>
                <p class="mt-2 text-sm">
                    Sidang
                </p>
            </div>

        </div>
    </div>

    {{-- DETAIL TUGAS AKHIR --}}
    <div class="bg-white rounded-xl border p-6">

        <h3 class="text-lg font-bold mb-4">
            Detail Tugas Akhir
        </h3>

        <table class="w-full">

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Judul
                </td>
                <td>
                    {{ $tugasAkhir->judul ?? '-' }}
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Topik
                </td>
                <td>
                    {{ $tugasAkhir->topik ?? '-' }}
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Pembimbing I
                </td>
                <td>
                    -
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Pembimbing II
                </td>
                <td>
                    -
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">
                    Status
                </td>
                <td>
                    <span class="bg-red-700 text-white px-3 py-1 rounded">
                        {{ $tugasAkhir->status ?? 'Belum Ada' }}
                    </span>
                </td>
            </tr>

            <tr>
                <td class="py-3 font-semibold">
                    Catatan Prodi
                </td>
                <td>
                    -
                </td>
            </tr>

        </table>

    </div>

</div>

@endsection