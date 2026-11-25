@extends('layouts.prodi')

@section('title', 'Kelola Jadwal - SIMTA')
@section('page-label', 'Kelola Jadwal')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h1 class="text-lg font-bold">Buat Jadwal</h1>
        <p class="mt-1 text-sm text-gray-500">Jadwal akan ditampilkan kepada mahasiswa yang dipilih.</p>
        <form action="{{ route('prodi.jadwal.store') }}" method="POST" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @csrf
            <label class="text-sm font-semibold">
                Mahasiswa
                <select name="user_id" required class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 font-normal">
                    <option value="">Pilih mahasiswa</option>
                    @foreach($mahasiswa as $item)
                        <option value="{{ $item->id }}" {{ old('user_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}{{ $item->nim ? ' (' . $item->nim . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-semibold">
                Jenis
                <select name="jenis" required class="mt-2 w-full rounded-md border border-gray-300 bg-white px-3 py-2 font-normal">
                    <option value="bimbingan">Bimbingan</option>
                </select>
            </label>
            <label class="text-sm font-semibold">
                Tanggal &amp; Waktu
                <input type="datetime-local" name="waktu" min="{{ now()->format('Y-m-d\TH:i') }}" required
                    value="{{ old('waktu') }}" class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 font-normal">
            </label>
            <label class="text-sm font-semibold">
                Lokasi
                <input type="text" name="lokasi" maxlength="255" required value="{{ old('lokasi') }}"
                    placeholder="Contoh: Ruang Seminar 1" class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 font-normal">
            </label>
            <label class="text-sm font-semibold">
                Dosen / Penguji
                <input type="text" name="dosen_penguji" maxlength="255" required value="{{ old('dosen_penguji') }}"
                    placeholder="Nama dosen atau tim penguji" class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 font-normal">
            </label>
            <div class="flex items-end">
                <button type="submit" class="rounded-md bg-[#841719] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6f1214]">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 p-5">
            <h2 class="font-bold">Jadwal dan Pendaftaran Mahasiswa</h2>
        </div>
        @forelse($jadwal as $item)
            <article class="grid gap-4 border-b border-gray-100 p-5 last:border-0 lg:grid-cols-[minmax(0,1fr)_minmax(320px,1fr)]">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="font-semibold">{{ $item->mahasiswa->name }}</h3>
                        <span class="rounded bg-gray-100 px-2 py-1 text-xs">{{ $item->mahasiswa->nim ?: 'NIM -' }}</span>
                        <span class="rounded px-2 py-1 text-xs font-semibold
                            {{ in_array($item->status, ['terjadwal', 'selesai'], true) ? 'bg-green-100 text-green-700' : ($item->status === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-700">
                        {{ ucfirst($item->jenis) }} — {{ $item->tugasAkhir?->judul ?? 'Bimbingan' }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $item->waktu?->format('d M Y, H.i') ?? 'Belum ditetapkan' }}
                        · {{ $item->lokasi ?? '-' }} · {{ $item->dosen_penguji ?? '-' }}
                    </p>
                    @if(str_starts_with($item->catatan ?? '', 'DEMO:'))
                        <p class="mt-2 text-xs font-medium text-blue-700">Data demo untuk pengujian tampilan.</p>
                    @elseif($item->catatan)
                        <p class="mt-2 text-sm text-red-700">Catatan: {{ $item->catatan }}</p>
                    @endif
                </div>

                @if(!in_array($item->status, ['ditolak', 'selesai'], true))
                    <div class="space-y-3">
                        <form action="{{ route('prodi.jadwal.tetapkan', $item) }}" method="POST"
                            class="grid gap-2 sm:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <label class="text-xs font-semibold sm:col-span-2">Tetapkan / Perbarui Jadwal</label>
                            <input type="datetime-local" name="waktu" required min="{{ now()->format('Y-m-d\TH:i') }}"
                                value="{{ old('waktu', $item->waktu?->format('Y-m-d\TH:i')) }}"
                                class="rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <input type="text" name="lokasi" required maxlength="255" value="{{ old('lokasi', $item->lokasi) }}"
                                placeholder="Lokasi" class="rounded-md border border-gray-300 px-3 py-2 text-sm">
                            <input type="text" name="dosen_penguji" required maxlength="255" value="{{ old('dosen_penguji', $item->dosen_penguji) }}"
                                placeholder="Dosen / Penguji" class="rounded-md border border-gray-300 px-3 py-2 text-sm sm:col-span-2">
                            <button type="submit" class="rounded-md bg-[#841719] px-4 py-2 text-sm font-semibold text-white sm:col-span-2">
                                {{ $item->status === 'terjadwal' ? 'Simpan Perubahan' : 'Tetapkan Jadwal' }}
                            </button>
                        </form>

                        @if($item->status === 'menunggu')
                            <form action="{{ route('prodi.jadwal.tolak', $item) }}" method="POST" class="flex flex-wrap gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="catatan" maxlength="2000" placeholder="Catatan penolakan (opsional)"
                                    class="min-w-[180px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                                <button type="submit" class="rounded-md border border-red-300 px-4 py-2 text-sm font-semibold text-red-700">
                                    Tolak
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
                @if($item->status === 'terjadwal' && $item->waktu?->isPast())
                    <form action="{{ route('prodi.jadwal.selesai', $item) }}" method="POST" class="lg:col-start-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-md border border-green-300 px-4 py-2 text-sm font-semibold text-green-700">
                            Tandai Selesai
                        </button>
                    </form>
                @endif
            </article>
        @empty
            <p class="p-8 text-center text-sm text-gray-500">Belum ada jadwal atau pendaftaran mahasiswa.</p>
        @endforelse
    </section>
</div>
@endsection
