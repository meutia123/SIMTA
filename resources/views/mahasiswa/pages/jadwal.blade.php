@extends('layouts.app')

@section('title', 'Jadwal - SIMTA')
@section('page-label', 'Jadwal')
@section('page-description', 'Jadwal Bimbingan, Sempro, dan Sidang')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap gap-3" role="group" aria-label="Filter jadwal">
        <button type="button" data-filter="semua" aria-pressed="true"
            class="jadwal-filter rounded-md bg-[#841719] px-6 py-2.5 text-sm font-semibold text-white">
            Semua
        </button>
        <button type="button" data-filter="sempro" aria-pressed="false"
            class="jadwal-filter rounded-md bg-[#4b5420] px-6 py-2.5 text-sm font-semibold text-white hover:bg-[#606b2d]">
            Sempro
        </button>
        <button type="button" data-filter="sidang" aria-pressed="false"
            class="jadwal-filter rounded-md bg-[#4b5420] px-6 py-2.5 text-sm font-semibold text-white hover:bg-[#606b2d]">
            Sidang / Komprehensif
        </button>
        <button type="button" data-filter="bimbingan" aria-pressed="false"
            class="jadwal-filter rounded-md bg-[#4b5420] px-6 py-2.5 text-sm font-semibold text-white hover:bg-[#606b2d]">
            Bimbingan
        </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-300 bg-white">
        <div class="overflow-x-auto px-5 sm:px-7">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-900">
                        <th scope="col" class="px-2 py-4 font-semibold">No</th>
                        <th scope="col" class="px-2 py-4 font-semibold">Jenis</th>
                        <th scope="col" class="px-2 py-4 font-semibold">Tanggal &amp; Waktu</th>
                        <th scope="col" class="px-2 py-4 font-semibold">Lokasi</th>
                        <th scope="col" class="px-2 py-4 font-semibold">Dosen / Penguji</th>
                        <th scope="col" class="px-2 py-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-800">
                    @forelse($jadwal as $item)
                        <tr data-jadwal="{{ $item->jenis }}">
                            <td class="px-2 py-5 font-semibold">{{ $loop->iteration }}</td>
                            <td class="px-2 py-5">
                                {{ $item->jenis === 'sempro' ? 'Seminar Proposal' : ($item->jenis === 'sidang' ? 'Sidang / Komprehensif' : 'Bimbingan') }}
                                @if(str_starts_with($item->catatan ?? '', 'DEMO:'))
                                    <span class="ml-1 rounded bg-blue-100 px-2 py-1 text-[10px] font-semibold text-blue-700">Data demo</span>
                                @endif
                            </td>
                            <td class="px-2 py-5">{{ $item->waktu?->format('d M Y, H.i') ?? 'Menunggu penetapan' }}</td>
                            <td class="px-2 py-5">{{ $item->lokasi ?? '-' }}</td>
                            <td class="px-2 py-5">{{ $item->dosen_penguji ?? '-' }}</td>
                            <td class="px-2 py-5">
                                <span class="rounded px-2 py-1 text-xs font-semibold
                                    {{ in_array($item->status, ['terjadwal', 'selesai'], true) ? 'bg-[#9db3ed] text-gray-900' : ($item->status === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                Belum ada jadwal atau pendaftaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filters = document.querySelectorAll('.jadwal-filter');
        const rows = document.querySelectorAll('[data-jadwal]');

        filters.forEach(function(button) {
            button.addEventListener('click', function() {
                const filter = this.dataset.filter;

                filters.forEach(function(item) {
                    const active = item === button;
                    item.setAttribute('aria-pressed', active ? 'true' : 'false');
                    item.classList.toggle('bg-[#841719]', active);
                    item.classList.toggle('bg-[#4b5420]', !active);
                });

                rows.forEach(function(row) {
                    row.classList.toggle('hidden', filter !== 'semua' && row.dataset.jadwal !== filter);
                });
            });
        });
    });
</script>
@endpush
