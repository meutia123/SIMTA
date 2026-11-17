
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMTA')</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f5f5f5] text-gray-900">

<div class="min-h-screen p-4 md:p-6">

    <!-- JUDUL HALAMAN -->
    <h2 class="text-lg text-gray-400 mb-3 ml-1">
        @yield('page-label', 'Dashboard')
    </h2>

    <div class="flex min-h-[570px]">

        <!-- SIDEBAR -->
        <aside class="w-40 md:w-[157px] bg-[#253653] text-white
                      flex flex-col shrink-0 p-4">

            <!-- LOGO -->
            <div class="flex items-center gap-3 mb-7 mt-1">

                <div class="bg-blue-600 border border-blue-300
                            rounded w-9 h-8 flex items-center justify-center">
                    <span class="text-lg">🎓</span>
                </div>

                <div>
                    <h1 class="font-bold text-sm">SIMTA</h1>
                    <p class="text-[7px] text-gray-300">
                        Sistem Informasi Tugas Akhir
                    </p>
                </div>

            </div>

            <!-- MENU SIDEBAR -->
            <nav class="space-y-2 text-[10px] font-semibold">

                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded
                   {{ request()->routeIs('dashboard') ? 'bg-blue-700 border border-blue-400' : 'hover:bg-blue-800' }}">
                    <span>⌂</span> Dashboard
                </a>

                <a href="{{ route('tugas-akhir') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded
                   {{ request()->routeIs('tugas-akhir') ? 'bg-blue-700 border border-blue-400' : 'hover:bg-blue-800' }}">
                    <span>▤</span> Tugas Akhir
                </a>

                <a href="{{ route('dokumen') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded
                   {{ request()->routeIs('dokumen') ? 'bg-blue-700 border border-blue-400' : 'hover:bg-blue-800' }}">
                    <span>📁</span> Dokumen
                </a>

                <a href="{{ route('jadwal') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded
                   {{ request()->routeIs('jadwal') ? 'bg-blue-700 border border-blue-400' : 'hover:bg-blue-800' }}">
                    <span>▣</span> Jadwal
                </a>

                <a href="{{ route('profil') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded
                   {{ request()->routeIs('profil') ? 'bg-blue-700 border border-blue-400' : 'hover:bg-blue-800' }}">
                    <span>♟</span> Profil
                </a>

            </nav>

            <!-- IDENTITAS MAHASISWA -->
            <div class="mt-auto pt-6">

                <div class="flex items-center gap-2">

                    <div class="w-7 h-7 rounded-full bg-blue-600
                                flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-[9px] font-bold truncate">
                            Mahasiswa
                        </p>

                        <p class="text-[8px] text-gray-300 truncate">
                            {{ Auth::user()->name }}
                        </p>
                    </div>

                    <!-- LOGOUT -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button type="submit"
                                title="Logout"
                                class="text-red-400 hover:text-red-200 text-sm">
                            ⇥
                        </button>
                    </form>

                </div>

            </div>

        </aside>

        <!-- KONTEN HALAMAN -->
        <main class="flex-1 min-w-0 bg-[#eeeaf6] p-3 md:p-4">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>
