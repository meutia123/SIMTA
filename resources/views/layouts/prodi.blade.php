<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMTA - Prodi')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f7f4] text-gray-900">
<div class="min-h-screen p-4 md:p-6">
    <div class="flex min-h-[calc(100vh-48px)]">
        <!-- SIDEBAR -->
        <aside class="w-52 md:w-60 bg-[#4b5420] text-white flex flex-col shrink-0 overflow-y-auto p-4">
            <div class="flex items-center gap-2 mb-10 mt-1">
                <div class="bg-[#841719] border border-white rounded w-9 h-8 flex items-center justify-center">
                    🎓
                </div>
                <div>
                    <h1 class="font-bold text-sm">SIMTA</h1>
                    <p class="text-[7px] text-gray-200">Sistem Informasi Tugas Akhir</p>
                </div>
            </div>
            <nav class="space-y-2 text-xs font-semibold">
                <a href="{{ route('prodi.dashboard') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.dashboard') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    🏠 Dashboard
                </a>
                <a href="{{ route('prodi.pengajuan-tugas-akhir') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.pengajuan-tugas-akhir*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    📄 Pengajuan Judul
                </a>
                <a href="{{ route('prodi.sempro') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.sempro') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▣ Kelola Sempro
                </a>
                <a href="{{ route('prodi.sidang') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.sidang') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▣ Sidang/Komprehensif
                </a>
                <a href="{{ route('prodi.validasi-nilai') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.validasi-nilai*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ◉ Validasi Nilai
                </a>
                <a href="{{ route('prodi.progres-mahasiswa') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.progres-mahasiswa') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ◴ Progres Mahasiswa
                </a>
                <a href="{{ route('prodi.rekap-dosen') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.rekap-dosen') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ◉ Rekap Keterlibatan
                </a>
                <a href="{{ route('prodi.laporan') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.laporan*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▤ Laporan
                </a>
                <a href="{{ route('prodi.jadwal') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.jadwal*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▣ Jadwal Bimbingan
                </a>
                <a href="{{ route('prodi.pengumuman') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.pengumuman*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▤ Pengumuman
                </a>
                <a href="{{ route('prodi.profil') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('prodi.profil') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ◉ Profil
                </a>
            </nav>
            <!-- IDENTITAS -->
            <div class="mt-auto pt-6">
                <div class="flex items-center gap-2 border border-[#737c3c] rounded-lg p-2">
                    <div class="w-7 h-7 rounded-full bg-[#841719] flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[9px] font-bold">Prodi</p>
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
                        @yield('page-label', 'Dashboard Prodi')
                    </h2>
                    <p class="text-[9px] text-gray-200">
                        @yield('page-description', 'Sistem Informasi Tugas Akhir')
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <form action="{{ route('prodi.pengajuan-tugas-akhir') }}" method="GET" class="hidden md:block">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari mahasiswa atau judul..."
                               aria-label="Cari pengajuan tugas akhir"
                               class="bg-[#4b5420] text-white placeholder-gray-200 rounded px-4 py-2 text-xs w-56">
                    </form>
                    <a href="{{ route('prodi.pengajuan-tugas-akhir', ['status' => 'Menunggu']) }}"
                       title="Pengajuan menunggu" aria-label="Pengajuan menunggu" class="relative text-sm">
                        🔔
                    </a>
                    <a href="{{ route('prodi.profil') }}" title="Profil {{ Auth::user()->name }}" class="w-6 h-6 overflow-hidden rounded-full bg-[#a34a4b] flex items-center justify-center text-xs">
                        @if(Auth::user()->foto_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->foto_path) }}"
                                 alt="" class="h-full w-full object-cover">
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
