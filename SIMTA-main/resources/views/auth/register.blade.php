<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SIMTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-white min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-gray-100 rounded-2xl p-8 shadow-sm my-6">
        <div class="flex justify-center mb-4">
            <i class="fa-solid fa-graduation-cap text-5xl text-black"></i>
        </div>
        <h1 class="text-center text-3xl font-bold text-black">SIMTA</h1>
        <p class="text-center text-sm font-semibold text-black mt-2 mb-6">Daftar Akun Baru</p>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-black mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan Nama"
                    class="w-full px-4 py-3 bg-white rounded-lg border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-black mb-2">NIM</label>
                <input type="text" name="nim" value="{{ old('nim') }}" placeholder="Masukkan NIM"
                    class="w-full px-4 py-3 bg-white rounded-lg border {{ $errors->has('nim') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                @error('nim')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-black mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan Email"
                    class="w-full px-4 py-3 bg-white rounded-lg border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-black mb-2">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Masukkan Password"
                        oninput="checkPassword()"
                        class="w-full px-4 pr-11 py-3 bg-white rounded-lg border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }} focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                    <button type="button" onclick="togglePwd('password','eye1')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400">
                        <i id="eye1" class="fa-regular fa-eye-slash"></i>
                    </button>
                </div>

                {{-- Indikator kekuatan password real-time --}}
                <div class="mt-2 space-y-1 text-xs">
                    <p id="rule-length" class="text-gray-500 flex items-center gap-1">
                        <i class="fa-solid fa-circle text-[6px]"></i> Minimal 8 karakter
                    </p>
                    <p id="rule-upper" class="text-gray-500 flex items-center gap-1">
                        <i class="fa-solid fa-circle text-[6px]"></i> Ada huruf besar (A-Z)
                    </p>
                    <p id="rule-lower" class="text-gray-500 flex items-center gap-1">
                        <i class="fa-solid fa-circle text-[6px]"></i> Ada huruf kecil (a-z)
                    </p>
                    <p id="rule-number" class="text-gray-500 flex items-center gap-1">
                        <i class="fa-solid fa-circle text-[6px]"></i> Ada angka (0-9)
                    </p>
                    <p id="rule-symbol" class="text-gray-500 flex items-center gap-1">
                        <i class="fa-solid fa-circle text-[6px]"></i> Ada simbol (!@#$%^&*)
                    </p>
                </div>

                @error('password')<p class="text-red-600 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-black mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi Password"
                        class="w-full px-4 pr-11 py-3 bg-white rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                    <button type="button" onclick="togglePwd('password_confirmation','eye2')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400">
                        <i id="eye2" class="fa-regular fa-eye-slash"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition mt-2">
                Daftar
            </button>
        </form>

        <p class="text-center text-sm font-semibold text-black mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="underline hover:text-blue-700">Login di sini</a>
        </p>
    </div>

    <script>
        function togglePwd(fieldId, iconId) {
            const input = document.getElementById(fieldId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye-slash','fa-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye','fa-eye-slash');
            }
        }

        function checkPassword() {
            const pwd = document.getElementById('password').value;
            setRule('rule-length', pwd.length >= 8);
            setRule('rule-upper',  /[A-Z]/.test(pwd));
            setRule('rule-lower',  /[a-z]/.test(pwd));
            setRule('rule-number', /[0-9]/.test(pwd));
            setRule('rule-symbol', /[!@#$%^&*(),.?":{}|<>_\-+=\/\\[\]`~';]/.test(pwd));
        }

        function setRule(id, ok) {
            const el = document.getElementById(id);
            if (ok) {
                el.classList.remove('text-gray-500');
                el.classList.add('text-green-600');
                el.querySelector('i').className = 'fa-solid fa-circle-check';
            } else {
                el.classList.remove('text-green-600');
                el.classList.add('text-gray-500');
                el.querySelector('i').className = 'fa-solid fa-circle text-[6px]';
            }
        }
    </script>
</body>
</html>