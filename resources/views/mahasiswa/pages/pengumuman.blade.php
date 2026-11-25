@extends('layouts.app')

@section('title', 'Pengumuman - SIMTA')
@section('page-label', 'Pengumuman')

@section('content')
<div class="space-y-4">
    @forelse($pengumuman as $item)
        <article class="rounded-xl border border-gray-200 bg-white p-5">
            <h1 class="text-lg font-bold">{{ $item->judul }}</h1>
            <p class="mt-1 text-xs text-gray-500">
                {{ $item->pembuat->name }} · {{ $item->created_at->format('d M Y H:i') }}
            </p>
            <div class="mt-4 whitespace-pre-line text-sm text-gray-700">{{ $item->isi }}</div>
        </article>
    @empty
        <div class="rounded-xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-500">
            Belum ada pengumuman.
        </div>
    @endforelse

    {{ $pengumuman->links() }}
</div>
@endsection
