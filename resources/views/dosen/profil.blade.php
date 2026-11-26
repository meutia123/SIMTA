@extends('layouts.dosen')

@section('title', 'Profil Dosen - SIMTA')
@section('page-label', 'Profil')
@section('page-description', 'Data diri dan keamanan akun')

@section('content')
<div class="space-y-4">
    @if(session('success'))
        <div class="rounded-lg bg-green-100 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-[minmax(240px,1fr)_minmax(0,1.8fr)]">
        <section class="flex min-h-[420px] flex-col items-center rounded-lg border border-gray-300 bg-white px-6 py-8 text-center">
            @if(Auth::user()->foto_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->foto_path) }}"
                     alt="Foto profil {{ Auth::user()->name }}" class="h-24 w-24 rounded-full object-cover">
            @else
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-[#841719] text-3xl font-semibold text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif
            <h1 class="mt-5 font-bold">{{ Auth::user()->name }}</h1>
            <p class="mt-2 text-sm text-gray-600">{{ Auth::user()->program_studi ?: 'Sistem Informasi' }}</p>
            <form action="{{ route('dosen.profil.foto') }}" method="POST" enctype="multipart/form-data" class="mt-5">
                @csrf
                <input type="file" name="foto" id="fotoProfil" accept=".jpg,.jpeg,.png,.webp" class="sr-only" required>
                <label for="fotoProfil" class="inline-flex cursor-pointer rounded-md border border-gray-300 bg-white px-7 py-2 text-sm font-semibold">Ubah Foto</label>
                <button type="submit" id="simpanFotoProfil" class="ml-2 hidden rounded-md bg-[#841719] px-4 py-2 text-sm font-semibold text-white">Simpan</button>
                <p class="mt-2 text-xs text-gray-500">JPG, PNG, atau WEBP; maksimal 2 MB.</p>
            </form>
        </section>

        <div class="space-y-4">
            <section class="rounded-lg border border-gray-300 bg-white px-5 sm:px-7">
                <h2 class="py-4 font-bold">Data Diri</h2>
                <dl class="divide-y divide-gray-200 text-sm">
                    <div class="grid grid-cols-[minmax(100px,0.3fr)_1fr] gap-3 py-3"><dt class="font-medium">Nama</dt><dd>{{ Auth::user()->name }}</dd></div>
                    <div class="grid grid-cols-[minmax(100px,0.3fr)_1fr] gap-3 py-3"><dt class="font-medium">Program Studi</dt><dd>{{ Auth::user()->program_studi ?: 'Sistem Informasi' }}</dd></div>
                    <div class="grid grid-cols-[minmax(100px,0.3fr)_1fr] gap-3 py-3"><dt class="font-medium">Email</dt><dd class="break-all">{{ Auth::user()->email }}</dd></div>
                    <div class="grid grid-cols-[minmax(100px,0.3fr)_1fr] gap-3 py-3"><dt class="font-medium">No HP</dt><dd>{{ Auth::user()->no_hp ?: '-' }}</dd></div>
                </dl>
            </section>
            <section class="rounded-lg border border-gray-300 bg-white px-5 py-4 sm:px-7">
                <h2 class="font-bold">Ganti Password</h2>
                <form action="{{ route('dosen.profil.password') }}" method="POST" class="mt-3 space-y-3">
                    @csrf
                    <label class="block text-sm font-medium">Password lama
                        <input type="password" name="current_password" autocomplete="current-password" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </label>
                    <label class="block text-sm font-medium">Password baru
                        <input type="password" name="password" autocomplete="new-password" minlength="8" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </label>
                    <label class="block text-sm font-medium">Konfirmasi password baru
                        <input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </label>
                    <button class="rounded-md bg-[#841719] px-6 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const foto = document.getElementById('fotoProfil');
        const simpan = document.getElementById('simpanFotoProfil');
        foto.addEventListener('change', function() {
            simpan.classList.toggle('hidden', !this.files.length);
        });
    });
</script>
@endpush
