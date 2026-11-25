@extends('layouts.app')

@section('title', 'Profil - SIMTA')
@section('page-label', 'Profil')
@section('page-description', 'Data diri dan keamanan akun')

@section('content')
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
        <h2 class="mt-5 font-bold text-gray-950">{{ Auth::user()->name }}</h2>
        <p class="mt-2 text-sm text-gray-800">{{ Auth::user()->nim ?: 'NIM belum tersedia' }}</p>
        <form action="{{ route('profil.foto') }}" method="POST" enctype="multipart/form-data" class="mt-5">
            @csrf
            <input type="file" name="foto" id="fotoProfil" accept=".jpg,.jpeg,.png,.webp" class="sr-only" required>
            <label for="fotoProfil" class="inline-flex cursor-pointer rounded-md border border-gray-300 bg-white px-8 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
                Ubah Foto
            </label>
            <button type="submit" id="simpanFotoProfil" class="ml-2 hidden rounded-md bg-[#841719] px-4 py-2 text-sm font-semibold text-white hover:bg-[#6f1214]">
                Simpan
            </button>
            <p class="mt-2 text-xs text-gray-500">JPG, PNG, atau WEBP; maksimal 2 MB.</p>
        </form>
    </section>

    <div class="space-y-4">
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

        <section class="rounded-lg border border-gray-300 bg-white px-5 sm:px-7">
            <h2 class="py-4 font-bold text-gray-950">Data Diri</h2>
            <dl class="divide-y divide-gray-200 text-sm">
                <div class="grid grid-cols-[minmax(100px,0.3fr)_1fr] gap-3 py-4">
                    <dt class="font-medium">NIM</dt>
                    <dd>{{ Auth::user()->nim ?: '-' }}</dd>
                </div>
                <div class="grid grid-cols-[minmax(100px,0.3fr)_1fr] gap-3 py-4">
                    <dt class="font-medium">Nama</dt>
                    <dd>{{ Auth::user()->name }}</dd>
                </div>
                <div class="grid grid-cols-[minmax(100px,0.3fr)_1fr] gap-3 py-4">
                    <dt class="font-medium">Email</dt>
                    <dd class="break-all">{{ Auth::user()->email }}</dd>
                </div>
            </dl>
            <form action="{{ route('profil.data-diri') }}" method="POST" class="space-y-4 border-t border-gray-200 py-5">
                @csrf
                @method('PUT')
                <label class="block text-sm font-medium">
                    Angkatan
                    <input type="number" name="angkatan" min="1980" max="{{ now()->year + 1 }}"
                        value="{{ old('angkatan', Auth::user()->angkatan) }}" required
                        class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2">
                </label>
                <label class="block text-sm font-medium">
                    Program Studi
                    <input type="text" name="program_studi" maxlength="150"
                        value="{{ old('program_studi', Auth::user()->program_studi) }}" required
                        class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2">
                </label>
                <label class="block text-sm font-medium">
                    No. HP
                    <input type="tel" name="no_hp" maxlength="20" inputmode="tel"
                        value="{{ old('no_hp', Auth::user()->no_hp) }}" required
                        class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2">
                </label>
                <button type="submit"
                    class="rounded-md bg-[#841719] px-6 py-2 text-sm font-semibold text-white hover:bg-[#6f1214]">
                    Simpan Data Diri
                </button>
            </form>
        </section>

        <section class="rounded-lg border border-gray-300 bg-white px-5 py-4 sm:px-7">
            <h2 class="font-bold text-gray-950">Ganti Password</h2>
            <form action="{{ route('profil.password') }}" method="POST" class="mt-3 space-y-4">
                @csrf
                <input type="password" name="current_password" aria-label="Password lama"
                    placeholder="Password lama" autocomplete="current-password" required
                    class="w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-sm placeholder:text-gray-400">
                <input type="password" name="password" aria-label="Password baru"
                    placeholder="Password baru (minimal 8 karakter)" autocomplete="new-password" required
                    class="w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-sm placeholder:text-gray-400">
                <input type="password" name="password_confirmation" aria-label="Konfirmasi password baru"
                    placeholder="Konfirmasi password baru" autocomplete="new-password" required
                    class="w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-sm placeholder:text-gray-400">
                <button type="submit"
                    class="rounded-md bg-[#841719] px-6 py-2 text-sm font-semibold text-white hover:bg-[#6f1214]">
                    Simpan Perubahan
                </button>
            </form>
        </section>
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
