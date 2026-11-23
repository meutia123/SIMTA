<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMTA')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f4f5f7] text-gray-900">
<div class="min-h-screen p-4">
    <h2 class="text-sm text-gray-400 mb-2">@yield('page-label', 'Dashboard Dosen')</h2>
    <div class="flex min-h-[650px] border border-gray-200 rounded-lg overflow-hidden bg-white">
        <aside class="w-44 bg-[#53632c] text-white flex flex-col shrink-0 p-4">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-lg border border-white/30 bg-[#a51d22] flex items-center justify-center">🎓</div>
                <div><h1 class="font-bold">SIMTA</h1><p class="text-[8px] text-gray-200">Sistem Informasi Tugas Akhir</p></div>
            </div>
            <nav class="space-y-2 text-xs font-semibold">
                <a href="{{ route('dosen.dashboard') }}" class="flex gap-2 px-3 py-2 rounded {{ request()->routeIs('dosen.dashboard') ? 'bg-[#a51d22] border border-white/30' : 'hover:bg-black/10' }}">⌂ <span>Dashboard</span></a>
                <a href="#" class="flex gap-2 px-3 py-2 rounded hover:bg-black/10">♟ <span>Mahasiswa Saya</span></a>
                <a href="#" class="flex gap-2 px-3 py-2 rounded hover:bg-black/10">▣ <span>Jadwal</span></a>
                <a href="#" class="flex gap-2 px-3 py-2 rounded hover:bg-black/10">▤ <span>Input Nilai</span></a>
                <a href="#" class="flex gap-2 px-3 py-2 rounded hover:bg-black/10">▥ <span>Rekap Keberhasilan</span></a>
                <a href="#" class="flex gap-2 px-3 py-2 rounded hover:bg-black/10">▧ <span>SK & Dokumen</span></a>
                <a href="#" class="flex gap-2 px-3 py-2 rounded hover:bg-black/10">♟ <span>Profil</span></a>
            </nav>
            <div class="mt-auto flex items-center gap-2 text-xs">
                <div class="w-8 h-8 rounded-full bg-red-700 flex items-center justify-center font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="min-w-0"><p class="font-bold">Dosen</p><p class="truncate text-[10px]">{{ Auth::user()->name }}</p></div>
                <form action="{{ route('logout') }}" method="POST" class="ml-auto">@csrf<button title="Logout" class="text-red-200">⇥</button></form>
            </div>
        </aside>
        <main class="flex-1 min-w-0 bg-[#f6f5fa]">
            <header class="h-20 bg-white border-b px-6 flex items-center justify-between">
                <div><h1 class="font-bold text-xl">Dashboard Dosen</h1><p class="text-xs text-gray-500">Ringkasan Mahasiswa dan Jadwal Kamu</p></div>
                <div class="flex items-center gap-3"><input type="search" placeholder="Cari..." class="hidden md:block w-64 rounded px-4 py-2 text-xs text-white bg-[#53632c] placeholder:text-gray-200"><button class="w-9 h-9 rounded-lg bg-red-50 text-red-700">◴</button><div class="w-9 h-9 rounded-full bg-red-400 text-white flex items-center justify-center">D</div></div>
            </header>
            <div class="p-6">@yield('content')</div>
        </main>
    </div>
</div>
</body>
</html>
