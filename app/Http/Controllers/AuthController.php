<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->input('login');
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';
        $user = User::where($fieldType, $loginInput)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => !$user ? 'Email atau NIM tidak terdaftar.' : 'Password salah.',
            ])->onlyInput('login');
        }

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        return match (trim($user->role)) {
            'mahasiswa' => redirect()->route('dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'prodi' => redirect()->route('prodi.dashboard'),
            'tu' => redirect()->route('tu.dashboard'),
            default => redirect('/login')->withErrors(['login' => 'Role akun tidak dikenali.']),
        };
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:50|unique:users,nim',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        ]);

        User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil, silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
