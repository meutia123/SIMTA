@extends('layouts.prodi')

@section('title', 'Pengajuan Tugas Akhir - SIMTA')
@section('page-label', 'Review Pengajuan Judul')

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

    @forelse($pengajuan as $item)
        <article class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-bold">{{ $item->judul }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $item->mahasiswa->name }} · {{ $item->mahasiswa->nim ?: 'NIM -' }}</p>
                    <p class="mt-2 text-sm"><span class="font-semibold">Topik:</span> {{ $item->topik }}</p>
                    @if($item->deskripsi)
                        <p class="mt-2 whitespace-pre-line text-sm text-gray-600">{{ $item->deskripsi }}</p>
                    @endif
                    <p class="mt-2 text-sm text-gray-600">
                        Pilihan mahasiswa: {{ $item->calonDosen1?->name ?? '-' }} / {{ $item->calonDosen2?->name ?? '-' }}
                    </p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold
                    {{ $item->status === 'Disetujui' ? 'bg-green-100 text-green-700' : ($item->status === 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ $item->status }}
                </span>
            </div>

            <form action="{{ route('prodi.pengajuan-tugas-akhir.update', $item) }}" method="POST"
                class="mt-5 grid gap-4 border-t border-gray-100 pt-4 sm:grid-cols-2 lg:grid-cols-4">
                @csrf
                @method('PUT')
                <label class="text-sm font-semibold">
                    Status
                    <select name="status" required class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 font-normal">
                        @foreach(['Menunggu', 'Disetujui', 'Ditolak'] as $status)
                            <option value="{{ $status }}" {{ old('status', $item->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm font-semibold">
                    Pembimbing I
                    <select name="dosen_pembimbing_1" class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 font-normal">
                        <option value="">Pilih dosen</option>
                        @foreach($dosen as $pengajar)
                            <option value="{{ $pengajar->id }}" {{ old('dosen_pembimbing_1', $item->dosen_pembimbing_1 ?? $item->calon_dosen_1) == $pengajar->id ? 'selected' : '' }}>
                                {{ $pengajar->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm font-semibold">
                    Pembimbing II
                    <select name="dosen_pembimbing_2" class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 font-normal">
                        <option value="">Pilih dosen</option>
                        @foreach($dosen as $pengajar)
                            <option value="{{ $pengajar->id }}" {{ old('dosen_pembimbing_2', $item->dosen_pembimbing_2 ?? $item->calon_dosen_2) == $pengajar->id ? 'selected' : '' }}>
                                {{ $pengajar->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm font-semibold sm:col-span-2 lg:col-span-1">
                    Catatan Prodi
                    <textarea name="catatan_prodi" rows="2" maxlength="2000"
                        class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 font-normal">{{ old('catatan_prodi', $item->catatan_prodi) }}</textarea>
                </label>
                <div class="sm:col-span-2 lg:col-span-4">
                    <button type="submit" class="rounded-md bg-[#841719] px-5 py-2.5 text-sm font-semibold text-white">
                        Simpan Review
                    </button>
                </div>
            </form>
        </article>
    @empty
        <div class="rounded-xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-500">
            Belum ada pengajuan judul tugas akhir.
        </div>
    @endforelse
</div>
@endsection
