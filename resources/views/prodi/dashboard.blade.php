@extends('layouts.prodi')

@section('title', 'Dashboard Prodi - SIMTA')
@section('page-label', 'Dashboard Prodi')
@section('page-description', 'Ringkasan pengajuan dan tahapan tugas akhir')

@section('content')
<div class="space-y-5">
    <section class="rounded-xl bg-[#841719] p-6 text-white flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm text-red-100">Halo, {{ Auth::user()->name }}!</p>
            <h1 class="mt-1 text-xl font-bold">Pantau proses tugas akhir mahasiswa</h1>
            <p class="mt-2 text-sm text-red-100">Periksa pengajuan, tetapkan pembimbing, dan kelola jadwal ujian.</p>
        </div>
        <a href="{{ route('prodi.pengajuan-tugas-akhir', ['status' => 'Menunggu']) }}"
           class="rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-[#841719] hover:bg-red-50">
            Periksa Pengajuan
        </a>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('prodi.validasi-nilai', ['status' => 'menunggu_validasi']) }}" class="rounded-xl border border-gray-200 bg-white p-4 hover:border-[#841719]">
            <p class="font-semibold">Validasi Nilai</p>
            <p class="mt-1 text-xs text-gray-500">{{ $statistics['nilai_menunggu_validasi'] }} nilai menunggu pemeriksaan Prodi.</p>
        </a>
        <a href="{{ route('prodi.progres-mahasiswa') }}" class="rounded-xl border border-gray-200 bg-white p-4 hover:border-[#841719]">
            <p class="font-semibold">Pantau Progres Mahasiswa</p>
            <p class="mt-1 text-xs text-gray-500">Lihat status terbaru setiap tahap tugas akhir.</p>
        </a>
        <a href="{{ route('prodi.laporan') }}" class="rounded-xl border border-gray-200 bg-white p-4 hover:border-[#841719]">
            <p class="font-semibold">Laporan</p>
            <p class="mt-1 text-xs text-gray-500">Ringkasan tahapan dan hasil yang tercatat.</p>
        </a>
        <a href="{{ route('prodi.rekap-dosen') }}" class="rounded-xl border border-gray-200 bg-white p-4 hover:border-[#841719]">
            <p class="font-semibold">Rekap Keterlibatan Dosen</p>
            <p class="mt-1 text-xs text-gray-500">Jumlah bimbingan dan keterlibatan sebagai penguji.</p>
        </a>
    </section>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Pengajuan Menunggu', $statistics['judul_menunggu'], route('prodi.pengajuan-tugas-akhir', ['status' => 'Menunggu']), 'Periksa judul'],
            ['Belum Ada Pembimbing', $statistics['belum_ada_pembimbing'], route('prodi.pengajuan-tugas-akhir', ['status' => 'Disetujui']), 'Tetapkan dosen'],
            ['Pendaftaran Sempro', $statistics['sempro_berjalan'], route('prodi.sempro'), 'Kelola Sempro'],
            ['Pendaftaran Sidang', $statistics['sidang_berjalan'], route('prodi.sidang'), 'Kelola Sidang'],
        ] as [$label, $count, $url, $action])
            <a href="{{ $url }}" class="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-[#841719]">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $count }}</p>
                <p class="mt-3 text-xs font-semibold text-[#841719]">{{ $action }} →</p>
            </a>
        @endforeach
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="font-bold">Progres Tahapan Tugas Akhir</h2>
                <p class="mt-1 text-xs text-gray-500">Jumlah mahasiswa yang tercatat pada setiap tahap.</p>
            </div>
            <p class="text-xs text-gray-500">Total mahasiswa: <span class="font-semibold text-gray-800">{{ $studentCount }}</span></p>
        </div>
        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach([
                ['Pengajuan Judul', $stageCounts['pengajuan'], route('prodi.pengajuan-tugas-akhir')],
                ['Pembimbing', $stageCounts['pembimbing'], route('prodi.pengajuan-tugas-akhir', ['status' => 'Disetujui'])],
                ['Sempro Terjadwal/Selesai', $stageCounts['sempro'], route('prodi.sempro')],
                ['Sidang Terjadwal/Selesai', $stageCounts['sidang'], route('prodi.sidang')],
                ['Sidang Lulus', $stageCounts['selesai'], route('prodi.sidang')],
            ] as [$label, $count, $url])
                <a href="{{ $url }}" class="block">
                    <div class="flex items-center justify-between gap-2 text-xs">
                        <span class="text-gray-600">{{ $label }}</span>
                        <span class="font-bold">{{ $count }}</span>
                    </div>
                    <div class="mt-2 h-2 rounded-full bg-gray-100">
                        <div class="h-2 rounded-full bg-[#596329]" style="width: {{ min(100, ($count / $maxStageCount) * 100) }}%"></div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-bold">Pengajuan Tugas Akhir Terbaru</h2>
                <p class="mt-1 text-xs text-gray-500">Data terkini dari pengajuan mahasiswa.</p>
            </div>
            <a href="{{ route('prodi.pengajuan-tugas-akhir') }}" class="text-xs font-semibold text-[#841719] hover:underline">
                Lihat semua
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">NIM</th>
                        <th class="p-3">Mahasiswa</th>
                        <th class="p-3">Judul</th>
                        <th class="p-3">Pembimbing</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($latestApplications as $item)
                        <tr>
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">{{ $item->mahasiswa->nim ?: '-' }}</td>
                            <td class="p-3">{{ $item->mahasiswa->name }}</td>
                            <td class="p-3">{{ $item->judul }}</td>
                            <td class="p-3">
                                {{ $item->dosenPembimbing1?->name ?? '-' }}
                                @if($item->dosenPembimbing2)
                                    <span class="text-gray-400">/</span> {{ $item->dosenPembimbing2->name }}
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-semibold text-gray-700">
                                    {{ $item->status === 'Disetujui' && $item->dosen_pembimbing_1 && $item->dosen_pembimbing_2 ? 'Pembimbing Ditetapkan' : $item->status }}
                                </span>
                            </td>
                            <td class="p-3">
                                <a href="{{ route('prodi.pengajuan-tugas-akhir.review', $item) }}" class="font-semibold text-[#841719] hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-gray-500">Belum ada data pengajuan tugas akhir.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
