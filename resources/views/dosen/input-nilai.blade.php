@extends('layouts.dosen')

@section('title', 'Input Nilai - SIMTA')
@section('page-label', 'Input Nilai')
@section('header-title', 'Input Nilai')
@section('header-subtitle', 'Input nilai sempro dan sidang mahasiswa')

@section('content')
@php
    $statusClass = [
        'Belum dinilai' => 'border-gray-300 bg-white text-gray-700',
        'Menunggu validasi' => 'border-yellow-400 bg-yellow-50 text-orange-600',
        'Tervalidasi' => 'border-green-300 bg-green-50 text-green-700',
        'Dikembalikan' => 'border-red-300 bg-red-50 text-red-700',
    ];
@endphp

<div class="space-y-5">
    {{-- Filter --}}
    <div class="flex flex-wrap gap-3">
        <button type="button"
                class="nilai-filter rounded-full border border-[#a51d22] bg-[#a51d22] px-9 py-2 text-sm font-semibold text-white"
                data-filter="semua">
            Semua
        </button>

        <button type="button"
                class="nilai-filter rounded-full border border-gray-300 bg-white px-9 py-2 text-sm font-semibold text-gray-700"
                data-filter="sempro">
            Sempro
        </button>

        <button type="button"
                class="nilai-filter rounded-full border border-gray-300 bg-white px-9 py-2 text-sm font-semibold text-gray-700"
                data-filter="sidang">
            Sidang / Komprehensif
        </button>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-lg bg-white p-4 shadow-sm">
        <table class="w-full min-w-[950px] text-sm">
            <thead>
                <tr class="bg-[#efeff9] text-left text-xs">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Kegiatan</th>
                    <th class="px-4 py-3">Mahasiswa</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Peran Saya</th>
                    <th class="px-4 py-3">Nilai Akhir</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($nilaiMahasiswa as $index => $item)
                    <tr class="nilai-row border-b border-gray-200"
                        data-jenis="{{ $item['jenis'] }}">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $item['kegiatan'] }}</td>
                        <td class="px-4 py-3">{{ $item['mahasiswa'] }}</td>
                        <td class="px-4 py-3">{{ $item['tanggal'] }}</td>
                        <td class="px-4 py-3">{{ $item['peran'] }}</td>

                        <td class="px-4 py-3 font-medium">
                            {{ $item['nilai'] ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="{{ $statusClass[$item['status']] }} inline-block rounded-full border px-3 py-1 text-xs font-semibold">
                                {{ $item['status'] }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">
                                @if ($item['status'] === 'Belum dinilai')
                                    <button type="button"
                                            class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-400"
                                            disabled>
                                        Lihat
                                    </button>

                                   <a href="{{ route('dosen.input-nilai.sempro', ['nama' => urlencode($item['mahasiswa'])]) }}"
                                   class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                   Input nilai
                                </a>
                                @elseif ($item['status'] === 'Menunggu validasi')
                                    <button type="button"
                                            class="lihat-button rounded-md border border-gray-300 px-3 py-1.5 text-xs"
                                            data-nama="{{ $item['mahasiswa'] }}"
                                            data-nilai="{{ $item['nilai'] }}">
                                        Lihat
                                    </button>

                                    <button type="button"
                                            class="nilai-button rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-400"
                                            disabled>
                                        -
                                    </button>
                                @elseif ($item['status'] === 'Tervalidasi')
                                    <button type="button"
                                            class="lihat-button rounded-md border border-gray-300 px-3 py-1.5 text-xs"
                                            data-nama="{{ $item['mahasiswa'] }}"
                                            data-nilai="{{ $item['nilai'] }}">
                                        Lihat
                                    </button>

                                    <button type="button"
                                            class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-400"
                                            disabled>
                                        -
                                    </button>
                                @elseif ($item['status'] === 'Dikembalikan')
                                    <button type="button"
                                            class="lihat-button rounded-md border border-gray-300 px-3 py-1.5 text-xs"
                                            data-nama="{{ $item['mahasiswa'] }}"
                                            data-nilai="{{ $item['nilai'] }}">
                                        Lihat
                                    </button>

                                    <button type="button"
                                            class="nilai-button rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700"
                                            data-nama="{{ $item['mahasiswa'] }}"
                                            data-status="{{ $item['status'] }}"
                                            data-nilai="{{ $item['nilai'] }}">
                                        Ubah nilai
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            Belum ada mahasiswa yang dapat dinilai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="rounded-lg bg-white px-4 py-3 text-xs text-gray-500 shadow-sm">
        Nilai yang kamu input akan divalidasi oleh prodi sebelum menjadi nilai resmi
    </div>
</div>

{{-- Modal nilai --}}
<div id="nilai-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
        <h2 id="nilai-modal-title" class="mb-5 text-lg font-bold">
            Input Nilai
        </h2>

        <form id="nilai-form">
            <label for="nilai-akhir" class="mb-2 block text-sm font-medium">
                Nilai Akhir
            </label>

            <input id="nilai-akhir"
                   type="number"
                   min="0"
                   max="100"
                   step="0.01"
                   placeholder="0-100"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2">

            <p id="nilai-mahasiswa"
               class="mt-2 text-xs text-gray-500">
            </p>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button"
                        id="nilai-batal"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm">
                    Batal
                </button>

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const filterButtons = document.querySelectorAll('.nilai-filter');
    const nilaiRows = document.querySelectorAll('.nilai-row');

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const filter = button.dataset.filter;

            filterButtons.forEach((item) => {
                item.classList.remove(
                    'border-[#a51d22]',
                    'bg-[#a51d22]',
                    'text-white'
                );
                item.classList.add(
                    'border-gray-300',
                    'bg-white',
                    'text-gray-700'
                );
            });

            button.classList.remove(
                'border-gray-300',
                'bg-white',
                'text-gray-700'
            );
            button.classList.add(
                'border-[#a51d22]',
                'bg-[#a51d22]',
                'text-white'
            );

            nilaiRows.forEach((row) => {
                row.style.display =
                    filter === 'semua' || row.dataset.jenis === filter
                        ? ''
                        : 'none';
            });
        });
    });

    const nilaiModal = document.getElementById('nilai-modal');
    const nilaiInput = document.getElementById('nilai-akhir');
    const nilaiMahasiswa = document.getElementById('nilai-mahasiswa');
    const nilaiModalTitle = document.getElementById('nilai-modal-title');

    document.querySelectorAll('.nilai-button').forEach((button) => {
        button.addEventListener('click', () => {
            nilaiModalTitle.textContent =
                button.dataset.status === 'Dikembalikan'
                    ? 'Ubah Nilai'
                    : 'Input Nilai';

            nilaiMahasiswa.textContent =
                'Mahasiswa: ' + button.dataset.nama;

            nilaiInput.value = button.dataset.nilai || '';

            nilaiModal.classList.remove('hidden');
            nilaiModal.classList.add('flex');
        });
    });

    document.getElementById('nilai-batal').addEventListener('click', () => {
        nilaiModal.classList.add('hidden');
        nilaiModal.classList.remove('flex');
    });

    document.getElementById('nilai-form').addEventListener('submit', (event) => {
        event.preventEvent();
        alert('Nilai berhasil disimpan. Untuk penyimpanan permanen, sambungkan form ini ke database.');
        nilaiModal.classList.add('hidden');
        nilaiModal.classList.remove('flex');
    });

    nilaiModal.addEventListener('click', (event) => {
        if (event.target === nilaiModal) {
            nilaiModal.classList.add('hidden');
            nilaiModal.classList.remove('flex');
        }
    });
</script>
@endsection