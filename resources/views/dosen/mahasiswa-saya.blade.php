@extends('layouts.dosen')

@section('title', 'Mahasiswa Saya - SIMTA')
@section('page-label', 'Mahasiswa Saya')

@section('content')
<div class="space-y-5">
    @php
        $mahasiswa = [
            [
                'nama' => 'Dia Febi Ayu',
                'nim' => '2417020078',
                'judul' => 'Rancang Bangun Sistem Informasi Tugas Akhir',
                'peran' => 'Pembimbing I',
                'status' => 'Sempro',
                'warna_status' => 'bg-yellow-400',
            ],
            [
                'nama' => 'Elva Fitri',
                'nim' => '2417020058',
                'judul' => 'Analisis Kepuasan Pengguna Aplikasi Akademik',
                'peran' => 'Pembimbing II',
                'status' => 'Pembimbing Ditentukan',
                'warna_status' => 'bg-[#a7aef7]',
            ],
            [
                'nama' => 'Meutia Sandrina',
                'nim' => '2417020096',
                'judul' => 'Sistem Pendukung Keputusan Pemilihan Dosen',
                'peran' => 'Pembimbing I',
                'status' => 'Sidang',
                'warna_status' => 'bg-green-400',
            ],
            [
                'nama' => 'Zoria Fitrioca',
                'nim' => '2417020095',
                'judul' => 'Implementasi E-Learning Berbasis Web',
                'peran' => 'Pembimbing II',
                'status' => 'Pengajuan Judul',
                'warna_status' => 'bg-gray-400',
            ],
        ];
    @endphp

    {{-- Filter --}}
    <div class="flex flex-wrap gap-3">
        <button type="button"
                class="filter-button rounded-lg bg-[#a51d22] px-10 py-3 text-sm font-semibold text-white"
                data-filter="semua">
            Semua
        </button>

        <button type="button"
                class="filter-button rounded-lg border border-gray-300 bg-white px-10 py-3 text-sm font-semibold"
                data-filter="pembimbing">
            Pembimbing
        </button>

        <button type="button"
                class="filter-button rounded-lg border border-gray-300 bg-white px-10 py-3 text-sm font-semibold"
                data-filter="penguji">
            Penguji
        </button>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-lg border border-gray-100 bg-white shadow-sm">
        <table class="w-full min-w-[920px] text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left text-xs">
                    <th class="px-6 py-5">No</th>
                    <th class="px-4 py-5">Nama</th>
                    <th class="px-4 py-5">Nim</th>
                    <th class="px-4 py-5">Judul Tugas Akhir</th>
                    <th class="px-4 py-5">Peran</th>
                    <th class="px-4 py-5">Status</th>
                    <th class="px-4 py-5 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($mahasiswa as $index => $item)
                    <tr class="mahasiswa-row border-b border-gray-200"
                        data-role="{{ str_contains(strtolower($item['peran']), 'pembimbing') ? 'pembimbing' : 'penguji' }}">
                        <td class="px-6 py-5">{{ $index + 1 }}</td>

                        <td class="px-4 py-5 font-medium">
                            {{ $item['nama'] }}
                        </td>

                        <td class="px-4 py-5">
                            {{ $item['nim'] }}
                        </td>

                        <td class="max-w-[260px] px-4 py-5">
                            {{ $item['judul'] }}
                        </td>

                        <td class="px-4 py-5">
                            {{ $item['peran'] }}
                        </td>

                        <td class="px-4 py-5">
                            <span class="{{ $item['warna_status'] }} rounded px-3 py-1 text-xs">
                                {{ $item['status'] }}
                            </span>
                        </td>

                        <td class="px-4 py-5 text-center">
                            <button type="button"
                                    class="detail-button rounded-lg border border-gray-400 px-5 py-1.5 text-xs"
                                    data-nama="{{ $item['nama'] }}"
                                    data-nim="{{ $item['nim'] }}"
                                    data-judul="{{ $item['judul'] }}"
                                    data-peran="{{ $item['peran'] }}"
                                    data-status="{{ $item['status'] }}">
                                Detail
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal detail --}}
<div id="detail-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg font-bold">Detail Mahasiswa</h2>

            <button type="button"
                    id="close-modal"
                    class="text-2xl leading-none text-gray-500">
                &times;
            </button>
        </div>

        <dl class="space-y-3 text-sm">
            <div class="grid grid-cols-[100px_1fr] gap-2">
                <dt class="text-gray-500">Nama</dt>
                <dd id="detail-nama" class="font-medium"></dd>
            </div>

            <div class="grid grid-cols-[100px_1fr] gap-2">
                <dt class="text-gray-500">NIM</dt>
                <dd id="detail-nim"></dd>
            </div>

            <div class="grid grid-cols-[100px_1fr] gap-2">
                <dt class="text-gray-500">Judul</dt>
                <dd id="detail-judul"></dd>
            </div>

            <div class="grid grid-cols-[100px_1fr] gap-2">
                <dt class="text-gray-500">Peran</dt>
                <dd id="detail-peran"></dd>
            </div>

            <div class="grid grid-cols-[100px_1fr] gap-2">
                <dt class="text-gray-500">Status</dt>
                <dd id="detail-status"></dd>
            </div>
        </dl>
    </div>
</div>

<script>
    const filterButtons = document.querySelectorAll('.filter-button');
    const mahasiswaRows = document.querySelectorAll('.mahasiswa-row');

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const filter = button.dataset.filter;

            filterButtons.forEach((item) => {
                item.classList.remove('bg-[#a51d22]', 'text-white', 'border-[#a51d22]');
                item.classList.add('border', 'border-gray-300', 'bg-white');
            });

            button.classList.remove('border', 'border-gray-300', 'bg-white');
            button.classList.add('bg-[#a51d22]', 'text-white', 'border-[#a51d22]');

            mahasiswaRows.forEach((row) => {
                const role = row.dataset.role;

                row.style.display =
                    filter === 'semua' || role === filter
                        ? ''
                        : 'none';
            });
        });
    });

    const modal = document.getElementById('detail-modal');

    document.querySelectorAll('.detail-button').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById('detail-nama').textContent = button.dataset.nama;
            document.getElementById('detail-nim').textContent = button.dataset.nim;
            document.getElementById('detail-judul').textContent = button.dataset.judul;
            document.getElementById('detail-peran').textContent = button.dataset.peran;
            document.getElementById('detail-status').textContent = button.dataset.status;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    document.getElementById('close-modal').addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
</script>
@endsection