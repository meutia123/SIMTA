@extends('layouts.dosen')

@section('title', 'Input Nilai - SIMTA')
@section('page-label', 'Input Nilai')
@section('page-description', 'Nilai ujian yang menjadi tanggung jawab Anda')

@section('content')
<section class="rounded-xl border border-gray-200 bg-white p-5">
    <h1 class="text-lg font-bold">Input Nilai</h1>
    <p class="mt-1 text-sm text-gray-500">Nilai ujian dapat dikirim setelah ujian ditandai selesai oleh Prodi.</p>
    <div class="mt-5 overflow-x-auto">
        <table class="w-full min-w-[780px] text-left text-sm">
            <thead class="bg-[#f5f3fb] text-xs text-gray-700">
                <tr><th class="p-3">No</th><th class="p-3">Kegiatan</th><th class="p-3">Mahasiswa</th><th class="p-3">Dosen Penilai</th><th class="p-3">Status Nilai</th><th class="p-3">Aksi</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($jadwal as $item)
                    @php
                        $status = $item->status_validasi_nilai;
                        $isAssignedEvaluator = !$status || (int) $item->dosen_penilai_id === (int) Auth::id();
                    @endphp
                    <tr>
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $item->jenis === 'sidang' ? 'Sidang/Komprehensif' : 'Sempro' }}</td>
                        <td class="p-3">
                            <p class="font-semibold">{{ $item->mahasiswa->name }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $item->tugasAkhir?->judul ?? '-' }}</p>
                        </td>
                        <td class="p-3">{{ $item->peran_dosen }}</td>
                        <td class="p-3">
                            @if($status === 'menunggu_validasi')
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Menunggu Validasi</span>
                            @elseif($status === 'tervalidasi' || $item->nilaiTervalidasi())
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Tervalidasi</span>
                            @elseif($status === 'dikembalikan')
                                <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Dikembalikan</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">Belum Diinput</span>
                            @endif
                        </td>
                        <td class="p-3">
                            @if($item->nilaiTervalidasi() || $status === 'menunggu_validasi' || !$isAssignedEvaluator)
                                <span class="text-xs text-gray-500">{{ $item->nilaiTervalidasi() ? 'Nilai resmi' : 'Menunggu dosen penilai' }}</span>
                            @else
                                <a href="{{ route('dosen.nilai', $item) }}" class="rounded-md bg-[#841719] px-3 py-1.5 text-xs font-semibold text-white">
                                    {{ $status === 'dikembalikan' ? 'Perbaiki' : 'Input Nilai' }}
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">Belum ada ujian selesai yang perlu dinilai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
