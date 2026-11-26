@extends('layouts.dosen')

@section('title', 'Input Nilai Sempro - SIMTA')
@section('page-label', 'Input Nilai')
@section('header-title', 'Input Nilai Sempro')
@section('header-subtitle', 'Isi nilai komponen penelitian')

@section('content')
<div class="space-y-5">
    <a href="{{ route('dosen.input-nilai') }}"
       class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm hover:bg-gray-50">
        <span aria-hidden="true">←</span>
        <span>Kembali</span>
    </a>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        {{-- Form nilai --}}
        <form id="form-nilai-sempro"
              action="#"
              method="POST"
              class="rounded-lg border border-gray-100 bg-white p-5 shadow-sm lg:col-span-2">
            @csrf

            <h2 class="mb-5 font-bold">Nilai Penilaian</h2>

            <div class="space-y-5">
                <div>
                    <label for="presentasi" class="mb-2 block text-xs font-semibold">
                        Presentasi (0-100)
                    </label>
                    <input id="presentasi"
                           name="presentasi"
                           type="number"
                           min="0"
                           max="100"
                           step="0.01"
                           placeholder="0-100"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs">
                </div>

                <div>
                    <label for="naskah" class="mb-2 block text-xs font-semibold">
                        Naskah / Laporan (0-100)
                    </label>
                    <input id="naskah"
                           name="naskah"
                           type="number"
                           min="0"
                           max="100"
                           step="0.01"
                           placeholder="0-100"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs">
                </div>

                <div>
                    <label for="penguasaan_materi" class="mb-2 block text-xs font-semibold">
                        Penguasaan Materi (0-100)
                    </label>
                    <input id="penguasaan_materi"
                           name="penguasaan_materi"
                           type="number"
                           min="0"
                           max="100"
                           step="0.01"
                           placeholder="0-100"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs">
                </div>

                <div class="border-b border-gray-300 px-1 py-2 text-xs">
                    <span class="font-semibold">Nilai Akhir</span>
                    <span id="nilai_akhir" class="ml-8">-</span>
                </div>

                <div>
                    <label for="keterangan" class="mb-2 block text-xs font-semibold">
                        Keterangan
                    </label>
                    <select id="keterangan"
                            name="keterangan"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs">
                        <option value="">Pilih Keterangan</option>
                        <option value="Lulus">Lulus</option>
                        <option value="Perbaikan">Perbaikan</option>
                        <option value="Tidak Lulus">Tidak Lulus</option>
                    </select>
                </div>

                <button type="submit"
                        class="rounded-md bg-[#a51d22] px-6 py-2 text-xs font-semibold text-white hover:bg-red-900">
                    Simpan Nilai
                </button>
            </div>
        </form>

        {{-- Data penilaian --}}
        <aside class="h-fit rounded-lg border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="mb-5 font-bold">Data Penilaian</h2>

            <dl class="divide-y divide-gray-300 text-xs">
                <div class="grid grid-cols-[100px_1fr] gap-3 py-3">
                    <dt class="text-gray-500">Mahasiswa</dt>
                    <dd class="font-medium">{{ $penilaian['nama'] }}</dd>
                </div>

                <div class="grid grid-cols-[100px_1fr] gap-3 py-3">
                    <dt class="text-gray-500">Kegiatan</dt>
                    <dd>{{ $penilaian['kegiatan'] }}</dd>
                </div>

                <div class="grid grid-cols-[100px_1fr] gap-3 py-3">
                    <dt class="text-gray-500">Tanggal</dt>
                    <dd>{{ $penilaian['tanggal'] }}</dd>
                </div>

                <div class="grid grid-cols-[100px_1fr] gap-3 py-3">
                    <dt class="text-gray-500">Peran Saya</dt>
                    <dd>{{ $penilaian['peran'] }}</dd>
                </div>

                <div class="grid grid-cols-[100px_1fr] gap-3 py-3">
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        <span class="rounded border border-gray-300 bg-gray-100 px-3 py-1 text-xs">
                            {{ $penilaian['status'] }}
                        </span>
                    </dd>
                </div>
            </dl>
        </aside>
    </div>
</div>

<script>
    const scoreInputs = [
        document.getElementById('presentasi'),
        document.getElementById('naskah'),
        document.getElementById('penguasaan_materi'),
    ];

    const nilaiAkhir = document.getElementById('nilai_akhir');

    function hitungNilaiAkhir() {
        const semuaTerisi = scoreInputs.every((input) => input.value.trim() !== '');

        if (!semuaTerisi) {
            nilaiAkhir.textContent = '-';
            return;
        }

        const values = scoreInputs.map((input) => Number(input.value));
        const semuaValid = values.every(
            (value) => Number.isFinite(value) && value >= 0 && value <= 100
        );

        if (!semuaValid) {
            nilaiAkhir.textContent = '-';
            return;
        }

        const rataRata = values.reduce((total, value) => total + value, 0) / values.length;
        nilaiAkhir.textContent = rataRata.toFixed(2);
    }

    scoreInputs.forEach((input) => {
        input.addEventListener('input', hitungNilaiAkhir);
    });

    document.getElementById('form-nilai-sempro').addEventListener('submit', (event) => {
        event.preventDefault();
        alert('Nilai belum disimpan ke database. Form ini masih perlu disambungkan ke route penyimpanan.');
    });
</script>
@endsection