<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMTA</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-white min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-[#F4F3FA] rounded-2xl p-8 shadow-sm">

        <div class="flex justify-center mb-4">
            <i class="fa-solid fa-graduation-cap text-5xl text-black"></i>
        </div>

        <h1 class="text-center text-3xl font-bold text-black">SIMTA</h1>

        <p class="text-center text-sm font-semibold text-black mt-2 mb-8">
            Sistem Informasi Manajemen Tugas Akhir
        </p>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 text-sm px-4 py-2 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="login" class="block text-sm font-semibold text-black mb-2">
                    Email atau Nim
                </label>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i class="fa-regular fa-envelope"></i>
                    </span>

                    <input
                        type="text"
                        id="login"
                        name="login"
                        value="{{ old('login') }}"
                        placeholder="Masukkan Email atau Nim"
                        class="w-full pl-11 pr-4 py-3 bg-white rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#841719] text-sm"
                        required>
                </div>

                @error('login')
                    <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-black mb-2">
                    Password
                </label>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan Password"
                        class="w-full pl-11 pr-11 py-3 bg-white rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#841719] text-sm"
                        required>

                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400">
                        <i id="eyeIcon" class="fa-regular fa-eye-slash"></i>
                    </button>
                </div>

                @error('password')
                    <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full bg-[#841719] hover:bg-[#6f1214] text-white font-semibold py-3 rounded-lg transition">
                Login
            </button>

          
            <div class="text-center mt-3">
    <a href="{{ route('forgot-password') }}"
       class="text-[#841719] hover:underline text-sm">
        Lupa Password?
    </a>
</div>

        </form>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>
</html>