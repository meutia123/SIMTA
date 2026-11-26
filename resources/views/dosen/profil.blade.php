@extends('layouts.dosen')

@section('title', 'Profil - SIMTA')
@section('page-label', 'Profil')
@section('header-title', 'Profil')
@section('header-subtitle', 'Data diri dan keamanan akun')

@section('content')
<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
    <div class="rounded-lg border bg-white p-8 text-center">
        @if (auth()->user()->foto)
            <img
                src="{{ asset('storage/' . auth()->user()->foto) }}"
                alt="Foto profil {{ $profil['nama'] }}"
                class="mx-auto h-24 w-24 rounded-full object-cover"
            >
        @else
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gray-500 text-3xl text-white">
                {{ strtoupper(substr($profil['nama'], 0, 1)) }}
            </div>
        @endif

        <h2 class="mt-4 font-bold">{{ $profil['nama'] }}</h2>
        <p class="mb-4 mt-1 text-xs text-gray-500">NIDN: {{ $profil['nidn'] }}</p>

        @if (session('success'))
            <p class="mt-3 text-sm text-green-600">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div class="mt-3 text-left text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form
            action="{{ route('dosen.profil.foto') }}"
            method="POST"
            enctype="multipart/form-data"
            class="mt-4"
        >
            @csrf

            <label
                for="foto"
                class="inline-block cursor-pointer rounded border px-5 py-2 text-sm"
            >
                Ubah Foto
            </label>

            <input
                id="foto"
                name="foto"
                type="file"
                accept="image/png,image/jpeg,image/webp"
                class="hidden"
                onchange="this.form.submit()"
            >
        </form>
    </div>

    <div class="space-y-4 lg:col-span-2">
        <div class="rounded-lg border bg-white p-6">
            <h2 class="mb-4 font-bold">Data Diri</h2>

            <dl class="divide-y text-xs">
                <div class="grid grid-cols-3 py-3">
                    <dt class="font-semibold">NIDN</dt>
                    <dd class="col-span-2">{{ $profil['nidn'] }}</dd>
                </div>
                <div class="grid grid-cols-3 py-3">
                    <dt class="font-semibold">Nama</dt>
                    <dd class="col-span-2">{{ $profil['nama'] }}</dd>
                </div>
                <div class="grid grid-cols-3 py-3">
                    <dt class="font-semibold">Program Studi</dt>
                    <dd class="col-span-2">{{ $profil['program_studi'] }}</dd>
                </div>
                <div class="grid grid-cols-3 py-3">
                    <dt class="font-semibold">Email</dt>
                    <dd class="col-span-2">{{ $profil['email'] }}</dd>
                </div>
                <div class="grid grid-cols-3 py-3">
                    <dt class="font-semibold">No HP</dt>
                    <dd class="col-span-2">{{ $profil['no_hp'] }}</dd>
                </div>
            </dl>
        </div>

        <form method="POST" action="#" class="rounded-lg border bg-white p-6">
            @csrf
            <h2 class="mb-4 font-bold">Ganti Password</h2>

            <div class="space-y-3">
                <input
                    type="password"
                    placeholder="Password lama"
                    class="w-full rounded border px-3 py-2 text-xs"
                >

                <input
                    type="password"
                    placeholder="Password baru"
                    class="w-full rounded border px-3 py-2 text-xs"
                >

                <button
                    type="submit"
                    class="rounded bg-[#a51d22] px-5 py-2 text-xs text-white"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection