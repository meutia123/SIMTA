@extends('layouts.prodi')

@section('title', 'Rekap Keterlibatan Dosen - SIMTA')
@section('page-label', 'Rekap Keterlibatan Dosen')
@section('page-description', 'Rekap bimbingan dan keterlibatan sebagai penguji')

@section('content')
<section class="rounded-xl border border-gray-200 bg-white p-5">
    <div>
        <h1 class="text-lg font-bold">Rekap Keterlibatan Dosen</h1>
        <p class="mt-1 text-sm text-gray-500">Jumlah mahasiswa bimbingan dan penugasan penguji yang tercatat di SIMTA.</p>
    </div>
    <form action="{{ route('prodi.rekap-dosen') }}" method="GET" class="mt-4 max-w-xs">
        <label class="block text-sm font-semibold">
            Periode
            <select name="periode" onchange="this.form.submit()"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-normal">
                @forelse($periods as $value => $label)
                    <option value="{{ $value }}" {{ $periode === $value ? 'selected' : '' }}>{{ $label }}</option>
                @empty
                    <option value="">Semua Periode</option>
                @endforelse
            </select>
        </label>
    </form>
    <div class="mt-5 overflow-x-auto">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="bg-gray-50 text-xs text-gray-600">
                <tr>
                    <th class="p-3">No</th>
                    <th class="p-3">Dosen</th>
                    <th class="p-3">Mahasiswa Bimbingan</th>
                    <th class="p-3">Penguji Sempro</th>
                    <th class="p-3">Penguji Sidang/Komprehensif</th>
                    <th class="p-3">Total Keterlibatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($lecturers as $row)
                    <tr>
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3 font-semibold">{{ $row['dosen']->name }}</td>
                        <td class="p-3">{{ $row['mahasiswa_bimbingan'] }}</td>
                        <td class="p-3">{{ $row['penguji_sempro'] }}</td>
                        <td class="p-3">{{ $row['penguji_sidang'] }}</td>
                        <td class="p-3 font-semibold">
                            {{ $row['mahasiswa_bimbingan'] + $row['penguji_sempro'] + $row['penguji_sidang'] }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">Belum ada akun dosen.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
