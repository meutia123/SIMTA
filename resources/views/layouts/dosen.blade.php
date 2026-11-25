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
            <nav class="space-y-3 text-[10px] font-semibold">
                <a href="{{ route('dosen.dashboard') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dosen.dashboard') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    🏠 Dashboard
                </a>
            </nav>
            <!-- IDENTITAS -->
            <div class="mt-auto pt-6">
                <div class="flex items-center gap-2 border border-[#737c3c] rounded-lg p-2">
                    <div class="w-7 h-7 rounded-full bg-[#841719] flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
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
                    <input type="text" placeholder="Cari..."
                           class="hidden md:block bg-[#4b5420] text-white placeholder-gray-200 rounded px-4 py-2 text-xs w-48">
                    <span class="text-sm">🔔</span>
                    <div class="w-6 h-6 rounded-full bg-[#a34a4b] flex items-center justify-center text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>
            <main class="flex-1 min-w-0 p-5">
                @yield('content')
            </main>
        </div>
    </div>
</div>
</body>
</html>
