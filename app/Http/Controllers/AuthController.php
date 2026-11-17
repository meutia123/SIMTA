<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login (dengan pesan error spesifik per field)
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Email atau NIM wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $loginInput = $request->input('login');
        $fieldType  = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';

        // Cari user berdasarkan email ATAU nim
        $user = User::where($fieldType, $loginInput)->first();

        // 1. Cek apakah user ada
        if (!$user) {
            return back()
                ->withErrors(['login' => 'Email atau NIM tidak terdaftar.'])
                ->onlyInput('login');
        }

        // 2. Cek apakah password cocok
        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['password' => 'Password salah.'])
                ->onlyInput('login');
        }

        // 3. Semua cocok → login
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    // Tampilkan halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses register dengan password kuat
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'nim'      => 'required|string|max:50|unique:users,nim',
            'email'    => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()      // Harus ada huruf
                    ->mixedCase()    // Harus ada huruf besar & kecil
                    ->numbers()      // Harus ada angka
                    ->symbols()      // Harus ada simbol
                    ->uncompromised(),  // Cek tidak bocor di data breach
            ],
        ], [
            // Pesan kustom bahasa Indonesia
            'name.required'      => 'Nama wajib diisi',
            'nim.required'       => 'NIM wajib diisi',
            'nim.unique'         => 'NIM sudah terdaftar',
            'email.required'     => 'Email wajib diisi',
            'email.email'        => 'Format email tidak valid',
            'email.unique'       => 'Email sudah terdaftar',
            'password.required'  => 'Password wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        User::create([
            'name'     => $request->name,
            'nim'      => $request->nim,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil, silakan login.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}