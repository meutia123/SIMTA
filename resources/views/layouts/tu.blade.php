<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMTA - TU')</title>
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
            <nav class="space-y-3 text-[10px] font-semibold">
                <a href="{{ route('tu.dashboard') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('tu.dashboard') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    🏠 Dashboard
                </a>
                <a href="{{ route('tu.administrasi-sk') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('tu.administrasi-sk', 'tu.proses-sk*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▤ Administrasi SK
                </a>
                <a href="{{ route('tu.dokumen') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('tu.dokumen*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▧ Dokumen Tugas Akhir
                </a>
                <a href="{{ route('tu.profil') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('tu.profil*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ♟ Profil
                </a>
            </nav>
            <!-- IDENTITAS -->
            <div class="mt-auto pt-6">
                <div class="flex items-center gap-2 border border-[#737c3c] rounded-lg p-2">
                    <a href="{{ route('tu.profil') }}" class="w-7 h-7 shrink-0 rounded-full bg-[#841719] flex items-center justify-center font-bold text-xs overflow-hidden">
                        @if(Auth::user()->foto_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->foto_path) }}"
                                 alt="" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </a>
                    <div class="min-w-0 flex-1">
                        <p class="text-[9px] font-bold">TU</p>
                        <p class="text-[8px] text-gray-200 truncate">
                            {{ Auth::user()->program_studi ?: 'Tata Usaha' }}
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
            @php($isDashboard = request()->routeIs('tu.dashboard'))
            <header class="{{ $isDashboard ? 'bg-white text-gray-900 border border-gray-200' : 'bg-[#841719] text-white' }} px-5 py-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-sm">
                        @yield('page-label', 'Dashboard TU')
                    </h2>
                    <p class="text-[9px] {{ $isDashboard ? 'text-gray-500' : 'text-gray-200' }}">
                        @yield('page-description', 'Sistem Informasi Tugas Akhir')
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <form action="{{ request()->routeIs('tu.dokumen*') ? route('tu.dokumen') : route('tu.administrasi-sk') }}" method="GET" role="search">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari..."
                               aria-label="Cari mahasiswa atau SK"
                               class="hidden md:block bg-[#4b5420] text-white placeholder-gray-200 rounded px-4 py-2 text-xs w-48">
                    </form>
                    <a href="{{ route('tu.administrasi-sk', ['status' => 'perlu']) }}"
                       class="text-sm" aria-label="Lihat SK yang perlu diproses">🔔</a>
                    <a href="{{ route('tu.profil') }}"
                       class="w-6 h-6 rounded-full bg-[#a34a4b] flex items-center justify-center text-xs text-white overflow-hidden"
                       aria-label="Profil {{ Auth::user()->name }}">
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
