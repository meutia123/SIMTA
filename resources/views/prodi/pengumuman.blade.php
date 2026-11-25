@extends('layouts.prodi')

@section('title', 'Pengumuman - SIMTA')
@section('page-label', 'Kelola Pengumuman')

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
        <h1 class="text-lg font-bold">Terbitkan Pengumuman</h1>
        <form action="{{ route('prodi.pengumuman.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <label class="block text-sm font-semibold">
                Judul
                <input type="text" name="judul" maxlength="255" value="{{ old('judul') }}" required
                    class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 font-normal">
            </label>
            <label class="block text-sm font-semibold">
                Isi
                <textarea name="isi" rows="5" maxlength="10000" required
                    class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 font-normal">{{ old('isi') }}</textarea>
            </label>
            <button type="submit" class="rounded-md bg-[#841719] px-5 py-2.5 text-sm font-semibold text-white">
                Terbitkan
            </button>
        </form>
    </section>

    <section class="space-y-3">
        <h2 class="font-bold">Pengumuman Terbit</h2>
        @forelse($pengumuman as $item)
            <article class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="font-bold">{{ $item->judul }}</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ $item->pembuat->name }} · {{ $item->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <form action="{{ route('prodi.pengumuman.destroy', $item) }}" method="POST"
                        onsubmit="return confirm('Hapus pengumuman ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-semibold text-red-700 hover:underline">Hapus</button>
                    </form>
                </div>
                <p class="mt-4 whitespace-pre-line text-sm text-gray-700">{{ $item->isi }}</p>
            </article>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-500">
                Belum ada pengumuman.
            </div>
        @endforelse
    </section>
</div>
@endsection
