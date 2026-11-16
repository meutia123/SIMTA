<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SIMTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-[#F4F3FA] rounded-2xl p-8 shadow-sm">

        <h1 class="text-center text-3xl font-bold text-black">
            Lupa Password
        </h1>

        <p class="text-center text-sm text-gray-600 mt-2 mb-8">
            Masukkan email yang terdaftar untuk menerima kode verifikasi
        </p>

        <form action="{{ route('verify-otp') }}" method="GET">

            <input
                type="email"
                name="email"
                placeholder="Masukkan Email"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg mb-5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>

            <button
                type="submit"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg">
                Kirim Kode Verifikasi
            </button>

        </form>

    </div>

</body>
</html>