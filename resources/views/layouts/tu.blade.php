
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMTA - TU')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f5f5f5] text-gray-900">
    <div class="min-h-screen p-4 md:p-6">

        <h2 class="text-lg text-gray-400 mb-3 ml-1">
            @yield('page-label', 'Dashboard TU')
        </h2>

        <div class="flex min-h-[570px]">

            <aside class="w-40 md:w-[157px] bg-[#253653] text-white flex flex-col shrink-0 p-4">

                <div class="flex items-center gap-3 mb-7 mt-1">
                    <div class="bg-blue-600 border border-blue-300 rounded w-9 h-8 flex items-center justify-center">
                        <span class="text-lg">SIMTA</span>
                    </div>
                    <div>
                        <h1 class="font-bold text-sm">SIMTA</h1>
                        <p class="text-[7px] text-gray-300">Sistem Informasi Tugas Akhir</p>
                    </div>
                </div>

                <nav class="space-y-2 text-[10px] font-semibold">
                    <a href="/tu/dashboard" class="block px-2 py-2 rounded hover:bg-blue-800">
                        Dashboard
                    </a>

                    <a href="/tu/administrasi-sk" class="block px-2 py-2 rounded hover:bg-blue-800">
                        Administrasi SK
                    </a>

                    <a href="/tu/profil" class="block px-2 py-2 rounded hover:bg-blue-800">
                        Profil
                    </a>
                </nav>

                <div class="mt-auto pt-6 text-xs">
                    Tata Usaha
                </div>
            </aside>

            <main class="flex-1 min-w-0 bg-[#eeeaf6] p-3 md:p-4">
                @yield('content')
            </main>

        </div>
    </div>
</body>
</html>
