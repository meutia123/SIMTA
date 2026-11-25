
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

                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    🏠 Dashboard
                </a>

                <a href="{{ route('tugas-akhir') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('tugas-akhir*') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    📄 Tugas Akhir
                </a>

                <a href="{{ route('dokumen') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dokumen') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    📁 Dokumen
                </a>

                <a href="{{ route('jadwal') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('jadwal') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ▣ Jadwal
                </a>

                <a href="{{ route('profil') }}"
                   class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('profil') ? 'bg-[#841719] border border-white' : 'hover:bg-[#606b2d]' }}">
                    ♟ Profil
                </a>

            </nav>

            <!-- IDENTITAS -->
            <div class="mt-auto pt-6">
                <div class="flex items-center gap-2 border border-[#737c3c] rounded-lg p-2">
                    <div class="w-7 h-7 rounded-full bg-[#841719] flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-[9px] font-bold">Mahasiswa</p>
                        <p class="text-[8px] text-gray-200 truncate">
                            Sistem Informasi
                        </p>
                    </div>

                    <button type="button" id="btnLogout" title="Logout" aria-label="Logout"
                        class="text-red-300 hover:text-white">
                        ⇥
                    </button>
                </div>
            </div>

        </aside>

        <!-- KONTEN -->
        <div class="flex-1 min-w-0 flex flex-col bg-[#f7f7f4]">

            <!-- HEADER MARUN -->
            <header class="bg-[#841719] text-white px-5 py-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-sm">
                        @yield('page-label', 'Dashboard')
                    </h2>
                    <p class="text-[9px] text-gray-200">
                        @yield('page-description', 'Sistem Informasi Tugas Akhir')
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <form action="{{ route('cari') }}" method="GET" role="search">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari..."
                            aria-label="Cari tugas akhir, dokumen, jadwal, atau pengumuman"
                            class="hidden md:block bg-[#4b5420] text-white placeholder-gray-200 rounded px-4 py-2 text-xs w-48">
                    </form>

                    <a href="{{ route('notifikasi') }}" class="relative text-sm" aria-label="Notifikasi">
                        🔔
                        @php($unreadNotifications = Auth::user()->unreadNotifications()->count())
                        @if($unreadNotifications > 0)
                            <span class="absolute -right-2 -top-2 min-w-4 rounded-full bg-red-600 px-1 text-center text-[9px] text-white">
                                {{ $unreadNotifications }}
                            </span>
                        @endif
                    </a>

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

<div id="modalLogout" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/55 p-4"
    role="dialog" aria-modal="true" aria-labelledby="logoutTitle">
    <div class="w-full max-w-lg rounded-xl bg-[#e2e1ce] px-8 py-10 text-center shadow-xl sm:px-10">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-xl bg-[#841719]/20 text-[#841719]">
            <svg aria-hidden="true" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5m5 5H3m9-9h6a2 2 0 012 2v14a2 2 0 01-2 2h-6" />
            </svg>
        </div>
        <h2 id="logoutTitle" class="mt-7 text-xl font-bold text-black">Keluar dari SIMTA?</h2>
        <p class="mx-auto mt-4 max-w-xs text-xs leading-relaxed text-gray-800">
            Kamu akan keluar dari akun. Pastikan data yang kamu isi sudah tersimpan.
        </p>
        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <button type="button" id="btnBatalLogout"
                class="w-full rounded-md border border-gray-300 bg-white px-5 py-2 text-sm font-semibold text-black hover:bg-gray-50">
                Batal
            </button>
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full rounded-md bg-[#841719] px-5 py-2 text-sm font-semibold text-white hover:bg-[#6f1214]">
                    Ya, Logout
                </button>
            </form>
        </div>
    </div>
</div>

@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalLogout');
        const openButton = document.getElementById('btnLogout');
        const cancelButton = document.getElementById('btnBatalLogout');

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        openButton.addEventListener('click', function() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            cancelButton.focus();
        });

        cancelButton.addEventListener('click', closeModal);
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
                openButton.focus();
            }
        });
    });
</script>
</body>
</html>
