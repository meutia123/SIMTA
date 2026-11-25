<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - SIMTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-white p-3 text-gray-950">
    <main class="mx-auto mt-0 w-full max-w-xl rounded-lg border border-gray-300 bg-[#e2e1ce] px-6 py-12 text-center sm:px-16">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#4b5420] text-white">
            <svg aria-hidden="true" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12l2.5 2.5L16 9" />
            </svg>
        </div>
        <h1 class="mt-4 text-lg font-bold">Kamu Sudah Logout</h1>
        <p class="mt-2 text-sm">Terima kasih sudah menggunakan SIMTA. Sampai jumpa lagi!</p>
        <a href="{{ route('login') }}"
            class="mt-5 inline-flex w-full items-center justify-center rounded-md bg-[#841719] px-5 py-2 text-sm font-semibold text-white hover:bg-[#6f1214]">
            Kembali ke Halaman Login
        </a>
    </main>
</body>
</html>
