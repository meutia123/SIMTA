@extends('layouts.prodi')

@section('title', $judul . ' - SIMTA')
@section('page-label', $judul)
@section('page-description', $description)

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

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-lg font-bold">{{ $judul }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
            </div>
            <form action="{{ route($jenis === 'sempro' ? 'prodi.sempro' : 'prodi.sidang') }}" method="GET" class="flex gap-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIM, judul"
                       class="w-52 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <button class="rounded-lg bg-[#596329] px-4 py-2 text-sm font-semibold text-white">Cari</button>
            </form>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-3">
            @foreach([
                ['Menunggu', $jadwal->where('status', 'menunggu')->count(), 'text-yellow-700'],
                ['Terjadwal', $jadwal->where('status', 'terjadwal')->count(), 'text-blue-700'],
                ['Selesai', $jadwal->where('status', 'selesai')->count(), 'text-green-700'],
            ] as [$label, $count, $color])
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">{{ $label }}</p>
                    <p class="mt-1 text-xl font-bold {{ $color }}">{{ $count }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Mahasiswa</th>
                        <th class="p-3">Judul Tugas Akhir</th>
                        <th class="p-3">Jadwal / Lokasi</th>
                        <th class="p-3">Tim Penguji</th>
                        <th class="p-3">Hasil</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jadwal as $item)
                        <tr class="align-top">
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">
                                <p class="font-semibold">{{ $item->mahasiswa->name }}</p>
                                <p class="mt-1 text-gray-500">{{ $item->mahasiswa->nim ?: '-' }}</p>
                            </td>
                            <td class="p-3">{{ $item->tugasAkhir?->judul ?? '-' }}</td>
                            <td class="p-3">
                                <p>{{ $item->waktu?->format('d M Y, H.i') ?? 'Belum ditentukan' }}</p>
                                <p class="mt-1 text-gray-500">{{ $item->lokasi ?? '-' }}</p>
                            </td>
                            <td class="p-3">{{ $item->penguji->pluck('name')->implode(', ') ?: ($item->dosen_penguji ?? '-') }}</td>
                            <td class="p-3">
                                @if($item->nilaiTervalidasi())
                                    <span class="font-semibold {{ $item->hasil === 'Lulus' ? 'text-green-700' : 'text-orange-700' }}">
                                        {{ $item->hasil }} ({{ rtrim(rtrim(number_format((float) $item->nilai, 2, '.', ''), '0'), '.') }})
                                    </span>
                                @elseif($item->status_validasi_nilai === 'menunggu_validasi')
                                    <span class="font-semibold text-amber-700">Menunggu Validasi Nilai</span>
                                @elseif($item->status_validasi_nilai === 'dikembalikan')
                                    <span class="font-semibold text-orange-700">Perlu Perbaikan Nilai</span>
                                @else
                                    <span class="text-gray-500">Belum dicatat</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="rounded-full px-2.5 py-1 font-semibold {{ $item->status === 'selesai' ? 'bg-green-100 text-green-700' : ($item->status === 'terjadwal' ? 'bg-blue-100 text-blue-700' : ($item->status === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="min-w-52 space-y-2">
                                    @if($item->status === 'menunggu')
                                        <details class="rounded border border-gray-200 p-2">
                                            <summary class="cursor-pointer font-semibold text-[#596329]">Tetapkan Jadwal</summary>
                                            <p class="mt-2 text-[10px] text-gray-500">
                                                Jadwal ditetapkan SEK Prodi pada sistem eksternal. Masukkan informasinya di sini agar tampil di SIMTA.
                                            </p>
                                            <form action="{{ route('prodi.jadwal.tetapkan', $item) }}" method="POST" class="mt-3 space-y-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="return_to" value="{{ $jenis }}">
                                                <label class="block text-[11px] font-semibold">
                                                    Waktu
                                                    <input type="datetime-local" name="waktu" required min="{{ now()->format('Y-m-d\TH:i') }}"
                                                           value="{{ old('waktu', $item->waktu?->format('Y-m-d\TH:i')) }}"
                                                           class="mt-1 w-full rounded border border-gray-300 px-2 py-1.5 font-normal">
                                                </label>
                                                <label class="block text-[11px] font-semibold">
                                                    Lokasi
                                                    <input type="text" name="lokasi" required maxlength="255" value="{{ old('lokasi', $item->lokasi) }}"
                                                           class="mt-1 w-full rounded border border-gray-300 px-2 py-1.5 font-normal">
                                                </label>
                                                <label class="block text-[11px] font-semibold">
                                                    Tim Penguji
                                                    <select name="dosen_penguji_ids[]" multiple required size="4"
                                                            class="mt-1 w-full rounded border border-gray-300 bg-white px-2 py-1.5 font-normal">
                                                        @foreach($dosen as $pengajar)
                                                            <option value="{{ $pengajar->id }}" {{ $item->penguji->contains('id', $pengajar->id) ? 'selected' : '' }}>
                                                                {{ $pengajar->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="mt-1 block font-normal text-gray-500">Pilih satu atau beberapa dosen.</span>
                                                </label>
                                                <button class="w-full rounded bg-[#841719] px-2 py-1.5 text-[11px] font-semibold text-white">Simpan Informasi Jadwal</button>
                                            </form>
                                        </details>
                                        <details class="rounded border border-gray-200 p-2">
                                            <summary class="cursor-pointer font-semibold text-red-700">Tolak Pendaftaran</summary>
                                            <form action="{{ route('prodi.jadwal.tolak', $item) }}" method="POST" class="mt-3 space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="return_to" value="{{ $jenis }}">
                                                <textarea name="catatan" rows="2" maxlength="2000" placeholder="Catatan untuk mahasiswa"
                                                          class="w-full rounded border border-gray-300 px-2 py-1.5"></textarea>
                                                <button class="w-full rounded bg-red-700 px-2 py-1.5 text-[11px] font-semibold text-white">Konfirmasi Tolak</button>
                                            </form>
                                        </details>
                                    @elseif($item->status === 'terjadwal')
                                        @if($item->waktu && $item->waktu->isPast())
                                            <form action="{{ route('prodi.jadwal.selesai', $item) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="return_to" value="{{ $jenis }}">
                                                <button class="rounded bg-[#596329] px-3 py-1.5 font-semibold text-white">Tandai Selesai</button>
                                            </form>
                                        @else
                                            <span class="text-gray-500">Menunggu waktu pelaksanaan</span>
                                        @endif
                                    @elseif($item->status === 'selesai')
                                        @if($item->status_validasi_nilai === 'menunggu_validasi')
                                            <a href="{{ route('prodi.validasi-nilai.show', $item) }}"
                                               class="inline-flex rounded bg-blue-600 px-3 py-1.5 font-semibold text-white">Periksa Nilai</a>
                                        @elseif($item->nilaiTervalidasi())
                                            <a href="{{ route('prodi.validasi-nilai.show', $item) }}"
                                               class="inline-flex rounded border border-gray-300 px-3 py-1.5 font-semibold text-gray-700">Lihat Nilai</a>
                                        @else
                                            <span class="text-gray-500">Menunggu nilai dari dosen</span>
                                        @endif
                                    @elseif($item->catatan)
                                        <p class="text-red-700">{{ $item->catatan }}</p>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif

                                    @if($item->sk_tim_penguji_file)
                                        <a href="{{ route('surat-keputusan.download', ['jenis' => 'penguji', 'id' => $item->id]) }}" class="block font-semibold text-[#841719] hover:underline">
                                            Unduh SK Tim Penguji
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="p-8 text-center text-gray-500">Belum ada pendaftaran {{ $jenis === 'sempro' ? 'Sempro' : 'Sidang' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
