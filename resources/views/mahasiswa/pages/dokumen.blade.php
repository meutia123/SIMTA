
@extends('layouts.app')

@section('title', 'Dokumen - SIMTA')
@section('page-label', 'Dokumen')

@section('content')
<div class="space-y-6">

    {{-- Judul halaman --}}
    <div>
        <h1 class="text-xl font-bold text-gray-800">Dokumen Saya</h1>
        <p class="text-sm text-gray-500 mt-1">
            Kelola dan lihat dokumen persyaratan tugas akhir Anda.
        </p>
    </div>

    {{-- Informasi dan tombol unggah --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="font-semibold text-gray-800">Daftar Dokumen Tugas Akhir</h2>
            <p class="text-sm text-gray-500 mt-1">
                Periksa status dokumen yang harus dipenuhi.
            </p>
        </div>

        <button
            type="button"
            id="btnTambahDokumen"
            class="bg-[#841719] hover:bg-[#6f1214] text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
            + Unggah Dokumen
        </button>
    </div>

    {{-- Notifikasi --}}
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

    {{-- Tabel dokumen --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">Daftar Dokumen</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-5 py-4 font-semibold">No</th>
                        <th class="px-5 py-4 font-semibold">Nama Dokumen</th>
                        <th class="px-5 py-4 font-semibold">Jenis</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 font-semibold">Ukuran</th>
                        <th class="px-5 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @php
                        $no = 1;
                    @endphp
                    @foreach($dokumenWajib as $dw)
                        @php
                            $doc = $documents->where('nama_dokumen', $dw['nama'])->first();
                        @endphp
                        <tr>
                            <td class="px-5 py-4">{{ $no++ }}</td>
                            <td class="px-5 py-4 font-medium">{{ $dw['nama'] }}</td>
                            <td class="px-5 py-4">{{ $dw['jenis'] }}</td>
                            <td class="px-5 py-4">
                                @if($doc)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($doc->status === 'diverifikasi')
                                            bg-green-100 text-green-700
                                        @elseif($doc->status === 'ditolak')
                                            bg-red-100 text-red-700
                                        @else
                                            bg-blue-100 text-blue-700
                                        @endif">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        Belum Tersedia
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($doc)
                                    {{ number_format($doc->file_size / 1024, 1) }} KB
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($doc)
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dokumen.download', $doc->id) }}"
                                           class="text-[#841719] font-semibold hover:underline text-sm">
                                            Unduh
                                        </a>
                                        <form action="{{ route('dokumen.hapus', $doc->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Yakin ingin menghapus dokumen ini?')"
                                                class="text-red-600 font-semibold hover:underline text-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Dokumen tambahan (non-wajib) --}}
    @if($documents->whereNotIn('nama_dokumen', collect($dokumenWajib)->pluck('nama'))->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mt-6">
            <div class="p-5 border-b border-gray-200">
                <h2 class="font-semibold text-gray-800">Dokumen Tambahan</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-4 font-semibold">No</th>
                            <th class="px-5 py-4 font-semibold">Nama Dokumen</th>
                            <th class="px-5 py-4 font-semibold">Jenis</th>
                            <th class="px-5 py-4 font-semibold">Status</th>
                            <th class="px-5 py-4 font-semibold">Ukuran</th>
                            <th class="px-5 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @php
                            $noTambahan = 1;
                        @endphp
                        @foreach($documents->whereNotIn('nama_dokumen', collect($dokumenWajib)->pluck('nama')) as $doc)
                            <tr>
                                <td class="px-5 py-4">{{ $noTambahan++ }}</td>
                                <td class="px-5 py-4 font-medium">{{ $doc->nama_dokumen }}</td>
                                <td class="px-5 py-4">{{ $doc->jenis_dokumen }}</td>
                                <td class="px-5 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($doc->status === 'diverifikasi')
                                            bg-green-100 text-green-700
                                        @elseif($doc->status === 'ditolak')
                                            bg-red-100 text-red-700
                                        @else
                                            bg-blue-100 text-blue-700
                                        @endif">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    {{ number_format($doc->file_size / 1024, 1) }} KB
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dokumen.download', $doc->id) }}"
                                           class="text-[#841719] font-semibold hover:underline text-sm">
                                            Unduh
                                        </a>
                                        <form action="{{ route('dokumen.hapus', $doc->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Yakin ingin menghapus dokumen ini?')"
                                                class="text-red-600 font-semibold hover:underline text-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

 {{-- Modal Unggah Dokumen --}}
<div id="modalUnggahDokumen" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-md mx-4 shadow-xl">
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Unggah Dokumen</h3>
            <button type="button" id="btnTutupModal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <form action="{{ route('dokumen.upload') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-5" id="formUnggahDokumen">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-2">
                    Nama Dokumen <span class="text-red-600">*</span>
                </label>
                <select name="nama_dokumen" id="selectNamaDokumen" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm bg-white focus:ring-2 focus:ring-[#841719] focus:outline-none">
                    <option value="">Pilih nama dokumen</option>
                    @foreach($dokumenWajib as $dw)
                        @php
                            $sudahAda = $documents->where('nama_dokumen', $dw['nama'])->first();
                        @endphp
                        @if(!$sudahAda)
                            <option value="{{ $dw['nama'] }}">{{ $dw['nama'] }} (Wajib)</option>
                        @endif
                    @endforeach
                    <option value="Lainnya">Lainnya (Tambahan)</option>
                </select>
            </div>

            <div id="divNamaDokumenLain" class="hidden">
                <label class="block text-sm font-semibold mb-2">
                    Nama Dokumen Lainnya <span class="text-red-600">*</span>
                </label>
                <input type="text" name="nama_dokumen_lain" id="inputNamaDokumenLain"
                    placeholder="Masukkan nama dokumen"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#841719] focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">
                    Jenis Dokumen <span class="text-red-600">*</span>
                </label>
                <input type="text" name="jenis_dokumen" id="inputJenisDokumen"
                    placeholder="Contoh: Proposal, Surat Keputusan, dll"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#841719] focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">
                    File Dokumen <span class="text-red-600">*</span>
                </label>
                <input type="file" name="file" id="inputFile"
                    accept=".pdf,.doc,.docx"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm bg-white focus:ring-2 focus:ring-[#841719] focus:outline-none" required>
                <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX (Max 10MB)</p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" id="btnBatalModal"
                    class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm">
                    Batal
                </button>

                <button type="submit"
                    class="bg-[#841719] hover:bg-[#681315] text-white px-6 py-2.5 rounded-lg text-sm font-semibold">
                    Unggah
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalUnggahDokumen');
        const btnTambah = document.getElementById('btnTambahDokumen');
        const btnTutup = document.getElementById('btnTutupModal');
        const btnBatal = document.getElementById('btnBatalModal');
        const selectNama = document.getElementById('selectNamaDokumen');
        const divNamaLain = document.getElementById('divNamaDokumenLain');
        const inputNamaLain = document.getElementById('inputNamaDokumenLain');
        const inputJenis = document.getElementById('inputJenisDokumen');

        function bukaModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function tutupModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            resetForm();
        }

        function resetForm() {
            document.getElementById('formUnggahDokumen').reset();
            divNamaLain.classList.add('hidden');
            inputNamaLain.removeAttribute('required');
        }

        btnTambah.addEventListener('click', bukaModal);
        btnTutup.addEventListener('click', tutupModal);
        btnBatal.addEventListener('click', tutupModal);

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                tutupModal();
            }
        });

        selectNama.addEventListener('change', function() {
            if (this.value === 'Lainnya') {
                divNamaLain.classList.remove('hidden');
                inputNamaLain.setAttribute('required', 'required');
                inputJenis.value = '';
            } else {
                divNamaLain.classList.add('hidden');
                inputNamaLain.removeAttribute('required');
                inputNamaLain.value = '';

                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value && selectedOption.value !== 'Lainnya') {
                    const jenisMap = {
                        'SK Pembimbing': 'Surat Keputusan',
                        'Proposal Tugas Akhir': 'Proposal',
                        'SK Tim Penguji': 'Surat Keputusan',
                        'Berita Acara': 'Berita Acara',
                        'Lembar Persetujuan': 'Lembar Persetujuan',
                    };
                    inputJenis.value = jenisMap[selectedOption.value] || '';
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                tutupModal();
            }
        });
    });
</script>
@endpush
