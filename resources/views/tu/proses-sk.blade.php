@extends('layouts.tu')

@section('title', 'Proses SK - SIMTA')
@section('page-label', 'Proses SK')
@section('page-description', 'Input nomor dan tanggal SK')

@section('content')
<div class="space-y-5">
    <a href="{{ route('tu.administrasi-sk') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50">
        <span aria-hidden="true">←</span>
        Kembali
    </a>

    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <section class="rounded-lg border border-gray-200 bg-white p-5 sm:p-6 lg:col-span-2">
            <h1 class="text-base font-bold">Dokumen SK - {{ $record->mahasiswa->name }}</h1>
            <p class="mb-5 mt-1 text-xs text-gray-500">
                Diminta pada {{ $requestedAt?->locale('id')->translatedFormat('d M Y') ?? '-' }}
            </p>

            <form action="{{ route('tu.proses-sk.store', ['jenis' => $jenis, 'id' => $record->id]) }}"
                  method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <label for="nomor_sk" class="block text-sm font-medium">
                    Nomor SK
                    <input id="nomor_sk" name="nomor_sk" type="text" value="{{ old('nomor_sk') }}"
                           placeholder="Contoh: 123/Un.13/2026" maxlength="255" required
                           class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                </label>

                <label for="jenis_sk" class="block text-sm font-medium">
                    Jenis SK
                    <select id="jenis_sk" disabled class="mt-1 w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm">
                        <option selected>{{ $jenisLabel }}</option>
                    </select>
                </label>

                <label for="tanggal_sk" class="block text-sm font-medium">
                    Tanggal SK
                    <input id="tanggal_sk" name="tanggal_sk" type="date" value="{{ old('tanggal_sk') }}"
                           required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                </label>

                <label for="file_sk" class="block text-sm font-medium">
                    Unggah file SK
                    <input id="file_sk" name="file_sk" type="file" accept=".pdf,application/pdf" required
                           class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm file:mr-3 file:rounded file:border-0 file:bg-gray-100 file:px-3 file:py-1.5">
                    <span class="mt-1 block text-xs font-normal text-gray-500">PDF maksimal 10 MB.</span>
                </label>

                <button type="submit"
                        class="rounded-md bg-[#841719] px-5 py-2 text-sm font-semibold text-white hover:bg-[#6f1214]">
                    Simpan dan Terbitkan
                </button>
            </form>
        </section>

        <aside class="h-fit rounded-lg bg-[#e6e2ef] p-4 text-xs leading-relaxed text-gray-600">
            Setelah diterbitkan, SK dapat diakses mahasiswa dan dosen terkait.
        </aside>
    </div>
</div>
@endsection
