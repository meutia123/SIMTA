
@extends('layouts.app')

@section('title', 'Ajukan Judul - SIMTA')
@section('page-label', 'Pengajuan Judul Tugas Akhir')

@section('content')

<div class="space-y-5">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Ajukan Judul Tugas Akhir</h2>
            <p class="text-sm text-gray-500 mt-1">Lengkapi formulir pengajuan judul tugas akhir Anda.</p>
        </div>
        <a href="{{ route('tugas-akhir') }}"
           class="bg-[#596329] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#454e20]">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-lg">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('tugas-akhir.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- FORM UTAMA -->
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">

                <h3 class="font-bold text-gray-800 text-lg border-b pb-4 mb-5">
                    Informasi Pengajuan
                </h3>

                <div class="mb-5">
                    <label class="block text-sm font-semibold mb-2">
                        Judul Tugas Akhir <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="judul"
                        value="{{ old('judul') }}"
                        maxlength="255" required
                        placeholder="Masukkan judul tugas akhir"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#841719] focus:outline-none">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold mb-2">
                        Topik Penelitian <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="topik"
                        value="{{ old('topik') }}"
                        maxlength="255" required
                        placeholder="Contoh: Sistem Informasi, Data Mining"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#841719] focus:outline-none">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold mb-2">
                        Deskripsi Singkat
                    </label>
                    <textarea name="deskripsi" rows="5"
                        placeholder="Jelaskan gambaran umum penelitian Anda..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#841719] focus:outline-none">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="border-t pt-5">
                    <h3 class="font-bold text-gray-800 mb-2">
                        Pilihan Dosen Pembimbing
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Pilih tiga calon dosen pembimbing sesuai preferensi Anda.
                    </p>

                    @for($i = 1; $i <= 3; $i++)
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2">
                                Pilihan {{ $i }} <span class="text-red-600">*</span>
                            </label>

                            <select name="calon_dosen_{{ $i }}" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm bg-white">
                                <option value="">Pilih dosen pembimbing</option>

                                @foreach($dosen as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('calon_dosen_'.$i) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endfor
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t pt-5">
                    <a href="{{ route('tugas-akhir') }}"
                       class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm">
                        Batal
                    </a>

                    <button type="submit"
                        class="bg-[#841719] hover:bg-[#681315] text-white px-6 py-2.5 rounded-lg text-sm font-semibold">
                        Kirim Pengajuan
                    </button>
                </div>

            </div>

            <!-- PANEL INFORMASI -->
            <div class="space-y-5">

                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-4">
                        Informasi Pengajuan
                    </h3>

                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Nama Mahasiswa</p>
                            <p class="font-semibold">{{ Auth::user()->name }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">NIM</p>
                            <p class="font-semibold">{{ Auth::user()->nim ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs">Status Pengajuan</p>
                            <span class="inline-block mt-1 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">
                                {{ $tugasAkhir->status ?? 'Belum Mengajukan' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-[#f1f2e9] border border-[#d9ddc6] rounded-xl p-5">
                    <h3 class="font-bold text-[#596329] mb-3">
                        Petunjuk Pengajuan
                    </h3>

                    <ul class="text-sm text-gray-700 space-y-3 list-disc pl-5">
                        <li>Gunakan judul yang jelas dan sesuai bidang penelitian.</li>
                        <li>Isi topik penelitian dengan tepat.</li>
                        <li>Tuliskan deskripsi singkat penelitian.</li>
                        <li>Pilih tiga calon dosen pembimbing yang berbeda.</li>
                        <li>Periksa kembali data sebelum mengirim pengajuan.</li>
                    </ul>
                </div>

            </div>
        </div>
    </form>

</div>

@endsection
