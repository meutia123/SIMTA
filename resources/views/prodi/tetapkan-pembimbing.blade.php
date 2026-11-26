@extends('layouts.prodi')

@section('title', 'Tetapkan Pembimbing - SIMTA')
@section('page-label', 'Tetapkan Pembimbing')
@section('page-description', 'Tetapkan dua dosen pembimbing untuk mahasiswa')

@section('content')
<div class="space-y-5">
    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <a href="{{ route('prodi.pengajuan-tugas-akhir', ['status' => 'Disetujui']) }}" class="text-sm font-semibold text-[#841719] hover:underline">← Kembali ke pengajuan disetujui</a>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Mahasiswa</p>
        <h1 class="mt-1 text-lg font-bold">{{ $tugasAkhir->mahasiswa->name }}</h1>
        <p class="mt-1 text-sm text-gray-500">NIM {{ $tugasAkhir->mahasiswa->nim ?: '-' }}</p>
        <div class="mt-4 rounded-lg bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Judul Tugas Akhir</p>
            <p class="mt-1 font-semibold">{{ $tugasAkhir->judul }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ $tugasAkhir->topik }}</p>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="font-bold">Calon Dosen yang Diusulkan</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-3">
            @forelse($rekomendasi as $item)
                <div class="rounded-lg border border-gray-200 p-3">
                    <p class="text-xs text-gray-500">Rekomendasi {{ $loop->iteration }}</p>
                    <p class="mt-1 font-semibold">{{ $item->name }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada akun dosen yang tersedia.</p>
            @endforelse
        </div>
    </section>

    <form action="{{ route('prodi.pengajuan-tugas-akhir.pembimbing.update', $tugasAkhir) }}" method="POST" class="rounded-xl border border-gray-200 bg-white p-5">
        @csrf
        @method('PUT')
        <h2 class="font-bold">Penetapan Pembimbing</h2>
        <p class="mt-1 text-xs text-gray-500">Pilih dua dosen yang berbeda. Mahasiswa akan menerima notifikasi setelah disimpan.</p>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @foreach([1, 2] as $number)
                <label class="text-sm font-semibold">
                    Dosen Pembimbing {{ $number }}
                    <select name="dosen_pembimbing_{{ $number }}" required class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 font-normal">
                        <option value="">Pilih dosen</option>
                        @foreach($dosen as $pengajar)
                            <option value="{{ $pengajar->id }}" {{ old('dosen_pembimbing_'.$number, $tugasAkhir->{'dosen_pembimbing_'.$number} ?? $tugasAkhir->{'calon_dosen_'.$number}) == $pengajar->id ? 'selected' : '' }}>
                                {{ $pengajar->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @endforeach
        </div>
        <div class="mt-5 flex justify-end">
            <button type="submit" class="rounded-lg bg-[#841719] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#681315]">
                Simpan Pembimbing
            </button>
        </div>
    </form>
</div>
@endsection
