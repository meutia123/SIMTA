<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMTA')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f4f5f7] text-gray-900">
<div class="min-h-screen w-full p-2 sm:p-3 md:p-4">
    <h2 class="mb-2 text-sm text-gray-400">
        @yield('page-label', 'Dashboard Dosen')
    </h2>

    <div class="flex min-h-[calc(100vh-4rem)] flex-col overflow-hidden rounded-lg border border-gray-200 bg-white md:flex-row">
        <aside class="flex w-full shrink-0 flex-col bg-[#53632c] p-3 text-white md:w-56 md:p-4 lg:w-64">
            <div class="mb-4 flex items-center gap-3 md:mb-8">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-white/30 bg-[#a51d22]">
                    🎓
                </div>

                <div>
                    <h1 class="font-bold">SIMTA</h1>
                    <p class="text-[8px] text-gray-200">
                        Sistem Informasi Tugas Akhir
                    </p>
                </div>
            </div>

            <nav class="grid grid-cols-2 gap-2 text-xs font-semibold md:block md:space-y-2">
                <a href="{{ route('dosen.dashboard') }}"
                   class="flex gap-2 rounded px-3 py-2 {{ request()->routeIs('dosen.dashboard') ? 'border border-white/30 bg-[#a51d22]' : 'hover:bg-black/10' }}">
                    <span>⌂</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('dosen.mahasiswa-saya') }}"
                   class="flex gap-2 rounded px-3 py-2 {{ request()->routeIs('dosen.mahasiswa-saya') ? 'border border-white/30 bg-[#a51d22]' : 'hover:bg-black/10' }}">
                    <span>♟</span>
                    <span>Mahasiswa Saya</span>
                </a>

                <a href="#" class="flex gap-2 rounded px-3 py-2 hover:bg-black/10">
                    <span>▣</span>
                    <span>Jadwal</span>
                </a>

                <a href="#" class="flex gap-2 rounded px-3 py-2 hover:bg-black/10">
                    <span>▤</span>
                    <span>Input Nilai</span>
                </a>

                <a href="#" class="flex gap-2 rounded px-3 py-2 hover:bg-black/10">
                    <span>▥</span>
                    <span>Rekap Keberhasilan</span>
                </a>

                <a href="#" class="flex gap-2 rounded px-3 py-2 hover:bg-black/10">
                    <span>▧</span>
                    <span>SK &amp; Dokumen</span>
                </a>

                <a href="#" class="flex gap-2 rounded px-3 py-2 hover:bg-black/10">
                    <span>♟</span>
                    <span>Profil</span>
                </a>
            </nav>

            <div class="mt-4 flex items-center gap-2 border-t border-white/20 pt-3 text-xs md:mt-auto md:pt-4">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-700 font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div class="min-w-0">
                    <p class="font-bold">Dosen</p>
                    <p class="truncate text-[10px]">{{ auth()->user()->name }}</p>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="ml-auto">
                    @csrf
                    <button type="submit" title="Logout" class="text-red-200">⇥</button>
                </form>
            </div>
        </aside>

        <main class="min-w-0 flex-1 bg-[#f6f5fa]">
            <header class="flex min-h-20 items-center justify-between gap-3 border-b bg-white px-3 sm:px-4 md:px-6">
                <div class="min-w-0">
                    <h1 class="text-lg font-bold md:text-xl">Dashboard Dosen</h1>
                    <p class="text-xs text-gray-500">
                        Ringkasan Mahasiswa dan Jadwal Kamu
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <input type="search"
                           placeholder="Cari..."
                           class="hidden w-64 rounded px-4 py-2 text-xs text-white bg-[#53632c] placeholder:text-gray-200 lg:block">

                    <button type="button"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-700">
                        ◴
                    </button>

                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-400 text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            <div class="min-w-0 p-3 sm:p-4 md:p-6">
                @yield('content')
            </div>
        </main>
    </div>
</div>
</body>
</html>