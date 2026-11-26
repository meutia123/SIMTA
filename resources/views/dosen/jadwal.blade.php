@extends('layouts.dosen')

@section('title', 'Jadwal - SIMTA')
@section('page-label', 'Jadwal')
@section('header-title', 'Jadwal')
@section('header-subtitle', 'Jadwal Seminar Proposal dan Sidang yang melibatkan kamu')

@section('content')
<div class="space-y-5">
    {{-- Filter jadwal --}}
    <div class="flex flex-wrap gap-3">
        <button type="button"
                class="jadwal-filter rounded-full border border-[#a51d22] bg-[#a51d22] px-9 py-2 text-sm font-semibold text-white"
                data-filter="semua">
            Semua
        </button>

        <button type="button"
                class="jadwal-filter rounded-full border border-gray-300 bg-white px-9 py-2 text-sm font-semibold text-gray-700"
                data-filter="Seminar Proposal">
            Sempro
        </button>

        <button type="button"
                class="jadwal-filter rounded-full border border-gray-300 bg-white px-9 py-2 text-sm font-semibold text-gray-700"
                data-filter="sidang">
            Sidang / Komprehensif
        </button>
    </div>

    {{-- Tabel jadwal --}}
    <div class="overflow-x-auto rounded-lg bg-white p-4 shadow-sm">
        <table class="w-full min-w-[900px] text-sm">
            <thead>
                <tr class="bg-[#eeedfa] text-left text-xs font-semibold">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Kegiatan</th>
                    <th class="px-4 py-3">Mahasiswa</th>
                    <th class="px-4 py-3">Tanggal &amp; Waktu</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Peran Saya</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($jadwal as $index => $item)
                    <tr class="jadwal-row border-b border-gray-200"
                        data-jenis="{{ $item['jenis'] }}">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium">{{ $item['kegiatan'] }}</td>
                        <td class="px-4 py-3">{{ $item['mahasiswa'] }}</td>
                        <td class="px-4 py-3">{{ $item['tanggal'] }}</td>
                        <td class="px-4 py-3">{{ $item['lokasi'] }}</td>
                        <td class="px-4 py-3">{{ $item['peran'] }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-full border border-gray-300 bg-[#eeeeff] px-4 py-1 text-xs font-semibold text-blue-700">
                                {{ $item['status'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Belum ada jadwal.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    const filterButtons = document.querySelectorAll('.jadwal-filter');
    const jadwalRows = document.querySelectorAll('.jadwal-row');

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const filter = button.dataset.filter;

            filterButtons.forEach((item) => {
                item.classList.remove(
                    'border-[#a51d22]',
                    'bg-[#a51d22]',
                    'text-white'
                );
                item.classList.add('border-gray-300', 'bg-white', 'text-gray-700');
            });

            button.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
            button.classList.add('border-[#a51d22]', 'bg-[#a51d22]', 'text-white');

            jadwalRows.forEach((row) => {
                row.style.display =
                    filter === 'semua' || row.dataset.jenis === filter
                        ? ''
                        : 'none';
            });
        });
    });
</script>
@endsection