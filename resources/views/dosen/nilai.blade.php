@extends('layouts.dosen')

@section('title', 'Input Nilai - SIMTA')
@section('page-label', 'Input Nilai Ujian')

@section('content')
<div class="space-y-5">
    <a href="{{ route('dosen.jadwal') }}" class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">
        ← Kembali ke Jadwal
    </a>

    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    @if($jadwal->status_validasi_nilai === 'dikembalikan')
        <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800">
            <p class="font-semibold">Nilai dikembalikan oleh Prodi.</p>
            <p class="mt-1">{{ $jadwal->catatan_validasi_nilai }}</p>
        </div>
    @endif

    <section class="max-w-3xl rounded-xl border border-gray-200 bg-white p-5">
        <h1 class="text-lg font-bold">Penilaian {{ $jadwal->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro' }}</h1>
        <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
            <div><dt class="text-gray-500">Mahasiswa</dt><dd class="font-semibold">{{ $jadwal->mahasiswa->name }}</dd></div>
            <div><dt class="text-gray-500">Judul</dt><dd class="font-semibold">{{ $jadwal->tugasAkhir?->judul ?? '-' }}</dd></div>
        </dl>

        <form action="{{ route('dosen.nilai.store', $jadwal) }}" method="POST" class="mt-6 space-y-4">
            @csrf
            @foreach([
                'nilai_presentasi' => 'Presentasi',
                'nilai_naskah' => 'Naskah / Laporan',
                'nilai_penguasaan' => 'Penguasaan Materi',
            ] as $field => $label)
                <label class="block text-sm font-semibold">
                    {{ $label }}
                    <input type="number" name="{{ $field }}" min="0" max="100" step="0.01" required
                           value="{{ old($field, $jadwal->{$field}) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 font-normal">
                </label>
            @endforeach

            <label class="block text-sm font-semibold">
                Keterangan
                <select name="hasil" required class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 font-normal">
                    <option value="">Pilih keterangan</option>
                    @foreach(['Lulus', 'Mengulang'] as $hasil)
                        <option value="{{ $hasil }}" {{ old('hasil', $jadwal->hasil) === $hasil ? 'selected' : '' }}>{{ $hasil }}</option>
                    @endforeach
                </select>
            </label>
            <p class="text-xs text-gray-500">
                Nilai akhir adalah rata-rata tiga komponen. Keterangan dipilih manual dan menunggu validasi Prodi.
            </p>
            <button class="rounded-lg bg-[#841719] px-5 py-2.5 text-sm font-semibold text-white">
                Kirim Nilai ke Prodi
            </button>
        </form>
    </section>
</div>
@endsection
