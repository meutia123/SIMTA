<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMTA')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f7f4] text-gray-900">
<div class="min-h-screen p-4 md:p-6">
    <div class="flex min-h-[calc(100vh-48px)]">
        <!-- SIDEBAR -->
        <aside class="w-40 md:w-[165px] bg-[#4b5420] text-white flex flex-col shrink-0 p-4">
            <div class="flex items-center gap-2 mb-10 mt-1">
                <div class="bg-[#841719] border border-white rounded w-9 h-8 flex items-center justify-center">
                    🎓
                </div>
                <div>
                    <h1 class="font-bold text-sm">SIMTA</h1>
                    <p class="text-[7px] text-gray-200">Sistem Informasi Tugas Akhir</p>
                </div>
            </div>
            <nav class="space-y-2 text-[10px] font-semibold">
                <a href="{{ route('dosen.dashboard') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dosen.dashboard') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    🏠 Dashboard
                </a>
                <a href="{{ route('dosen.mahasiswa') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dosen.mahasiswa*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ♧ Mahasiswa Saya
                </a>
                <a href="{{ route('dosen.jadwal') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dosen.jadwal') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▣ Jadwal
                </a>
                <a href="{{ route('dosen.input-nilai') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dosen.input-nilai', 'dosen.nilai*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▤ Input Nilai
                </a>
                <a href="{{ route('dosen.keterlibatan') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dosen.keterlibatan') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ◉ Rekap Keterlibatan
                </a>
                <a href="{{ route('dosen.dokumen') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dosen.dokumen*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▰ SK &amp; Dokumen
                </a>
                <a href="{{ route('dosen.profil') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dosen.profil') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ● Profil
                </a>
            </nav>
            <!-- IDENTITAS -->
            <div class="mt-auto pt-6">
                <div class="flex items-center gap-2 border border-[#737c3c] rounded-lg p-2">
                        <a href="{{ route('dosen.profil') }}" title="Profil {{ Auth::user()->name }}"
                           class="flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-[#841719] text-xs font-bold">
                            @if(Auth::user()->foto_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->foto_path) }}" alt="" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </a>
                    <div class="min-w-0 flex-1">
                        <p class="text-[9px] font-bold">Dosen</p>
                        <p class="text-[8px] text-gray-200 truncate">
                            Sistem Informasi
                        </p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Logout" class="text-red-300">
                            ⇥
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        <!-- KONTEN -->
        <div class="flex-1 min-w-0 flex flex-col bg-[#f7f7f4]">
            <header class="bg-[#841719] text-white px-5 py-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-sm">
                        @yield('page-label', 'Dashboard Dosen')
                    </h2>
                    <p class="text-[9px] text-gray-200">
                        Sistem Informasi Tugas Akhir
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <form action="{{ request()->routeIs('dosen.mahasiswa*') ? route('dosen.mahasiswa') : route('dosen.jadwal') }}" method="GET">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari mahasiswa atau judul..."
                               aria-label="Cari jadwal dosen"
                               class="hidden w-52 rounded bg-[#4b5420] px-4 py-2 text-xs text-white placeholder-gray-200 md:block">
                    </form>
                    <a href="{{ route('dosen.input-nilai') }}" class="text-sm" aria-label="Input nilai">♧</a>
                    <a href="{{ route('dosen.profil') }}" title="Profil {{ Auth::user()->name }}" class="flex h-6 w-6 items-center justify-center overflow-hidden rounded-full bg-[#a34a4b] text-xs">
                        @if(Auth::user()->foto_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->foto_path) }}" alt="" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </a>
                </div>
            </header>
            <main class="flex-1 min-w-0 p-5">
                @yield('content')
            </main>
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
