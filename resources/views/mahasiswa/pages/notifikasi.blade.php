@extends('layouts.app')

@section('title', 'Notifikasi - SIMTA')
@section('page-label', 'Notifikasi')

@section('content')
<div class="space-y-3">
    @forelse($notifications as $notification)
        <a href="{{ $notification->data['url'] ?? route('dashboard') }}"
            class="block rounded-lg border border-gray-200 bg-white p-4 hover:border-[#841719]">
            <p class="text-sm font-semibold">{{ $notification->data['message'] ?? 'Notifikasi SIMTA' }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $notification->created_at->format('d M Y H:i') }}</p>
        </a>
    @empty
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center text-sm text-gray-500">
            Belum ada notifikasi.
        </div>
    @endforelse

    {{ $notifications->links() }}
</div>
@endsection
