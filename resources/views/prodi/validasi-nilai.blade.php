@extends('layouts.prodi')

@section('title', 'Validasi Nilai - SIMTA')
@section('page-label', 'Validasi Nilai')
@section('page-description', 'Periksa nilai Sempro dan Sidang/Komprehensif dari dosen')

@section('content')
<div class="space-y-5">
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div>
            <h1 class="text-lg font-bold">Validasi Nilai</h1>
            <p class="mt-1 text-sm text-gray-500">Nilai yang dikirim dosen perlu divalidasi sebelum menjadi hasil resmi mahasiswa.</p>
        </div>

        <nav class="mt-5 flex flex-wrap gap-2" aria-label="Filter status nilai">
            @foreach([
                'semua' => 'Semua',
                'menunggu_validasi' => 'Menunggu Validasi',
                'tervalidasi' => 'Tervalidasi',
                'dikembalikan' => 'Dikembalikan',
            ] as $value => $label)
                <a href="{{ route('prodi.validasi-nilai', ['status' => $value]) }}"
                   @class([
                       'rounded-lg border px-4 py-2 text-sm font-semibold',
                       'border-[#841719] bg-[#841719] text-white' => $status === $value,
                       'border-gray-300 bg-white text-gray-700 hover:border-[#841719]' => $status !== $value,
                   ])>
                    {{ $label }}
                    <span class="ml-1 {{ $status === $value ? 'text-red-100' : 'text-gray-500' }}">
                        {{ $counts[$value] }}
                    </span>
                </a>
            @endforeach
        </nav>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Kegiatan</th>
                        <th class="p-3">Mahasiswa</th>
                        <th class="p-3">Dosen Penilai</th>
                        <th class="p-3">Nilai Akhir</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jadwal as $item)
                        @php
                            $nilaiStatus = $item->status_validasi_nilai ?? 'tervalidasi';
                            $statusLabel = [
                                'menunggu_validasi' => 'Menunggu Validasi',
                                'tervalidasi' => 'Tervalidasi',
                                'dikembalikan' => 'Dikembalikan',
                            ][$nilaiStatus] ?? 'Belum Diketahui';
                            $statusClass = [
                                'menunggu_validasi' => 'bg-amber-100 text-amber-800',
                                'tervalidasi' => 'bg-green-100 text-green-700',
                                'dikembalikan' => 'bg-red-100 text-red-700',
                            ][$nilaiStatus] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <tr>
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">{{ $item->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro' }}</td>
                            <td class="p-3">
                                <p class="font-semibold">{{ $item->mahasiswa->name }}</p>
                                <p class="mt-1 text-gray-500">{{ $item->tugasAkhir?->judul ?? '-' }}</p>
                            </td>
                            <td class="p-3">
                                @if($item->dosenPenilai)
                                    {{ $item->dosenPenilai->name }}
                                    <span class="text-gray-500">
                                        ({{ (int) $item->tugasAkhir?->dosen_pembimbing_1 === (int) $item->dosen_penilai_id
                                            ? 'Pembimbing 1'
                                            : ((int) $item->tugasAkhir?->dosen_pembimbing_2 === (int) $item->dosen_penilai_id ? 'Pembimbing 2' : 'Penguji') }})
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-3 font-semibold">{{ number_format((float) $item->nilai, 2) }}</td>
                            <td class="p-3">
                                <span class="rounded-full px-2.5 py-1 font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="p-3">
                                <a href="{{ route('prodi.validasi-nilai.show', $item) }}"
                                   class="rounded bg-blue-600 px-3 py-1.5 font-semibold text-white hover:bg-blue-700">
                                    {{ $nilaiStatus === 'menunggu_validasi' ? 'Periksa' : 'Lihat' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-gray-500">Belum ada nilai pada status ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
