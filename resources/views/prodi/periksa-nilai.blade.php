@extends('layouts.prodi')

@section('title', 'Periksa Nilai - SIMTA')
@section('page-label', 'Periksa Nilai')
@section('page-description', 'Validasi nilai Sempro dan Sidang dari dosen')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <a href="{{ route('prodi.validasi-nilai') }}" class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">
        ← Kembali
    </a>

    <div class="grid gap-5 xl:grid-cols-[1.4fr_1fr]">
        <section class="rounded-xl border border-gray-200 bg-white p-5">
            <h1 class="text-lg font-bold">Nilai Penilaian - {{ $jadwal->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro' }}</h1>
            <dl class="mt-4 divide-y divide-gray-100 text-sm">
                @foreach([
                    'Presentasi' => $jadwal->nilai_presentasi,
                    'Naskah / Laporan' => $jadwal->nilai_naskah,
                    'Penguasaan Materi' => $jadwal->nilai_penguasaan,
                    'Nilai Akhir' => $jadwal->nilai,
                    'Keterangan' => $jadwal->hasil,
                ] as $label => $value)
                    <div class="flex justify-between gap-4 py-2.5">
                        <dt>{{ $label }}</dt>
                        <dd class="font-semibold">
                            @if($value === null)
                                -
                            @elseif($label === 'Keterangan')
                                {{ $value }}
                            @else
                                {{ number_format((float) $value, 2) }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>

            @if($jadwal->catatan_validasi_nilai)
                <div class="mt-5 rounded-lg border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800">
                    <p class="font-semibold">Catatan untuk dosen</p>
                    <p class="mt-1">{{ $jadwal->catatan_validasi_nilai }}</p>
                </div>
            @endif

            @if($jadwal->status_validasi_nilai === 'menunggu_validasi')
                <form action="{{ route('prodi.validasi-nilai.return', $jadwal) }}" method="POST" class="mt-6 space-y-3">
                    @csrf
                    @method('PATCH')
                    <label class="block text-sm font-semibold">
                        Catatan untuk Dosen
                        <textarea name="catatan_validasi_nilai" rows="3" maxlength="2000" required
                                  placeholder="Wajib diisi jika nilai dikembalikan"
                                  class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 font-normal">{{ old('catatan_validasi_nilai') }}</textarea>
                    </label>
                    <button class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">
                        Kembalikan ke Dosen
                    </button>
                </form>

                <form action="{{ route('prodi.validasi-nilai.validate', $jadwal) }}" method="POST" class="mt-3">
                    @csrf
                    @method('PATCH')
                    <button class="rounded-lg bg-[#841719] px-5 py-2.5 text-sm font-semibold text-white">
                        Validasi Nilai
                    </button>
                </form>
            @elseif($jadwal->status_validasi_nilai === 'tervalidasi' || $jadwal->nilaiTervalidasi())
                <p class="mt-5 rounded-lg bg-green-50 p-3 text-sm text-green-800">
                    Nilai yang sudah divalidasi menjadi nilai resmi dan tidak dapat diubah dosen.
                </p>
            @else
                <p class="mt-5 rounded-lg bg-orange-50 p-3 text-sm text-orange-800">
                    Nilai telah dikembalikan dan menunggu perbaikan dari dosen penilai.
                </p>
            @endif
        </section>

        <div class="space-y-5">
            <section class="rounded-xl border border-gray-200 bg-white p-5">
                <h2 class="font-bold">Data Penilaian</h2>
                <dl class="mt-3 divide-y divide-gray-100 text-sm">
                    <div class="flex justify-between gap-3 py-2"><dt>Mahasiswa</dt><dd class="text-right font-medium">{{ $jadwal->mahasiswa->name }}</dd></div>
                    <div class="flex justify-between gap-3 py-2"><dt>Kegiatan</dt><dd class="text-right font-medium">{{ $jadwal->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro' }}</dd></div>
                    <div class="flex justify-between gap-3 py-2">
                        <dt>Dosen Penilai</dt>
                        <dd class="text-right font-medium">
                            @if($jadwal->dosenPenilai)
                                {{ $jadwal->dosenPenilai->name }}
                                ({{ (int) $jadwal->tugasAkhir?->dosen_pembimbing_1 === (int) $jadwal->dosen_penilai_id
                                    ? 'Pembimbing 1'
                                    : ((int) $jadwal->tugasAkhir?->dosen_pembimbing_2 === (int) $jadwal->dosen_penilai_id ? 'Pembimbing 2' : 'Penguji') }})
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between gap-3 py-2"><dt>Status</dt><dd class="text-right font-medium">{{ str_replace('_', ' ', ucfirst($jadwal->status_validasi_nilai ?? 'tervalidasi')) }}</dd></div>
                </dl>
            </section>
            <p class="rounded-lg bg-gray-100 p-4 text-xs text-gray-600">
                Nilai akhir dihitung dari rata-rata komponen. Keterangan Lulus/Mengulang ditentukan secara manual, bukan dari ambang otomatis.
            </p>
        </div>
    </div>
</div>
@endsection
