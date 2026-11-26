@extends('layouts.prodi')

@section('title', 'Progres Mahasiswa - SIMTA')
@section('page-label', 'Progres Mahasiswa')
@section('page-description', 'Pantau perkembangan tugas akhir setiap mahasiswa')

@section('content')
<div class="space-y-5">
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-lg font-bold">Progres Tugas Akhir Mahasiswa</h1>
                <p class="mt-1 text-sm text-gray-500">Progres dihitung dari status pengajuan, pembimbing, jadwal, dan hasil ujian terbaru.</p>
            </div>
            <a href="{{ route('prodi.laporan') }}" class="rounded-lg bg-[#841719] px-4 py-2 text-sm font-semibold text-white hover:bg-[#681315]">
                Lihat Laporan
            </a>
        </div>

        <nav class="mt-5 flex flex-wrap gap-2" aria-label="Filter tahap progres">
            @foreach([
                'semua' => 'Semua',
                'pengajuan-judul' => 'Pengajuan Judul',
                'pembimbing' => 'Pembimbing',
                'sempro' => 'Sempro',
                'sidang' => 'Sidang',
                'selesai' => 'Selesai',
            ] as $value => $label)
                <a href="{{ route('prodi.progres-mahasiswa', array_filter(['q' => $filters['q'], 'tahap' => $value === 'semua' ? null : $value])) }}"
                   @class([
                       'rounded-lg border px-4 py-2 text-sm font-semibold',
                       'border-[#841719] bg-[#841719] text-white' => $filters['tahap'] === $value,
                       'border-gray-300 bg-white text-gray-700 hover:border-[#841719]' => $filters['tahap'] !== $value,
                   ])>
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <form action="{{ route('prodi.progres-mahasiswa') }}" method="GET" class="mt-4 flex flex-wrap gap-3">
            @if($filters['tahap'] !== 'semua')
                <input type="hidden" name="tahap" value="{{ $filters['tahap'] }}">
            @endif
            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari nama, NIM, atau judul"
                   class="min-w-[260px] flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-[#596329] px-4 py-2 text-sm font-semibold text-white">Cari Mahasiswa</button>
        </form>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[780px] text-left text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">NIM</th>
                        <th class="p-3">Tahap</th>
                        <th class="p-3">Progres</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $row)
                        <tr>
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">
                                <span class="font-semibold">{{ $row['tugasAkhir']->mahasiswa->name }}</span>
                            </td>
                            <td class="p-3">{{ $row['tugasAkhir']->mahasiswa->nim ?: '-' }}</td>
                            <td class="p-3 font-semibold">{{ $row['tahap'] }}</td>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-28 rounded-full bg-gray-200" role="progressbar"
                                         aria-label="Progres {{ $row['tugasAkhir']->mahasiswa->name }}"
                                         aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $row['persentase'] }}">
                                        <div class="h-2 rounded-full bg-blue-600" style="width: {{ $row['persentase'] }}%"></div>
                                    </div>
                                    <span class="text-gray-500">{{ $row['persentase'] }}%</span>
                                </div>
                            </td>
                            <td class="p-3">
                                @if($row['tahap_key'] === 'selesai')
                                    <span class="rounded-full bg-green-100 px-2.5 py-1 font-semibold text-green-700">Selesai</span>
                                @elseif($row['tugasAkhir']->status === 'Ditolak')
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 font-semibold text-red-700">Perlu Tindakan</span>
                                @else
                                    <span class="rounded-full bg-indigo-100 px-2.5 py-1 font-semibold text-indigo-700">Berjalan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-gray-500">Tidak ada data progres yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
