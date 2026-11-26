<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($request->input('login'));

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'nim';

        $user = User::where($fieldType, $loginInput)->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withErrors([
                    'password' => !$user
                        ? 'Email atau NIM tidak terdaftar.'
                        : 'Password salah.',
                ])
                ->onlyInput('login');
        }

        $role = strtolower(trim((string) $user->role));

        if (!in_array($role, ['mahasiswa', 'dosen', 'prodi', 'tu'], true)) {
            return back()
                ->withErrors(['login' => 'Role akun tidak dikenali.'])
                ->onlyInput('login');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return match ($role) {
            'mahasiswa' => redirect()->route('dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'prodi' => redirect()->route('prodi.dashboard'),
            'tu' => redirect()->route('tu.dashboard'),
        };
    }

    public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->with('logout_success', 'Terima kasih sudah menggunakan SIMTA, sampai jumpa lagi.');
}
}