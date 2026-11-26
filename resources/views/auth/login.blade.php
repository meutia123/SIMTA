<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-100">
    <main class="flex min-h-screen items-center justify-center p-4">
        <form method="POST"
              action="{{ route('login.process') }}"
              class="w-full max-w-md rounded-lg bg-white p-6 shadow">

            @csrf

            <h1 class="mb-6 text-center text-2xl font-bold">Login SIMTA</h1>

            @if ($errors->has('login'))
                <div class="mb-4 rounded bg-red-100 p-3 text-sm text-red-700">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <div class="mb-4">
                <label for="login" class="mb-1 block text-sm font-medium">
                    Email atau NIM
                </label>

                <input id="login"
                       type="text"
                       name="login"
                       value="{{ old('login') }}"
                       required
                       autofocus
                       class="w-full rounded border border-gray-300 px-3 py-2">
            </div>

            <div class="mb-4">
                <label for="password" class="mb-1 block text-sm font-medium">
                    Password
                </label>

                <input id="password"
                       type="password"
                       name="password"
                       required
                       class="w-full rounded border border-gray-300 px-3 py-2">

                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="mb-5 flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" value="1">
                Ingat saya
            </label>

            <button type="submit"
                    class="w-full rounded bg-[#53632c] px-4 py-2 font-semibold text-white">
                Login
            </button>
        </form>
    </main>

    {{-- Modal sukses logout --}}
    @if (session('logout_success'))
        <div id="logout-success-modal"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4">
            <div class="w-full max-w-sm rounded-lg border border-gray-300 bg-[#f1f0dc] p-8 text-center shadow-xl">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#53632c] text-2xl text-white">
                    ✓
                </div>

                <h2 class="mt-4 text-sm font-bold">Kamu Sudah Logout</h2>

                <p class="mt-2 text-xs text-gray-600">
                    {{ session('logout_success') }}
                </p>

                <button type="button"
                        id="logout-success-close"
                        class="mt-5 w-full rounded-md bg-[#a51d22] px-4 py-2 text-xs font-semibold text-white hover:bg-red-900">
                    Kembali ke Halaman Login
                </button>
            </div>
        </div>

        <script>
            document.getElementById('logout-success-close').addEventListener('click', () => {
                document.getElementById('logout-success-modal').remove();
            });
        </script>
    @endif
</body>
</html>