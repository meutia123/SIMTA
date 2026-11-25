<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

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
        ], [
            'login.required' => 'Email atau NIM wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $loginInput = $request->input('login');

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'nim';

        $user = User::where($fieldType, $loginInput)->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'login' => 'Email atau NIM tidak terdaftar.'
                ])
                ->onlyInput('login');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors([
                    'password' => 'Password salah.'
                ])
                ->onlyInput('login');
        }

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        switch (strtolower(trim((string) $user->role))) {
            case 'mahasiswa':
                return redirect()->route('dashboard');

            case 'dosen':
                return redirect()->route('dosen.dashboard');

            case 'prodi':
                return redirect()->route('prodi.dashboard');
            case 'tu':
                return redirect()->route('tu.dashboard');

            default:
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors([
                    'login' => 'Role akun tidak dikenali.'
                ]);
        }
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = PasswordBroker::sendResetLink($request->only('email'));

        if (in_array($status, [PasswordBroker::RESET_LINK_SENT, PasswordBroker::INVALID_USER], true)) {
            return back()->with('status', 'Jika email terdaftar, tautan reset password akan dikirim.');
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.new-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $status = PasswordBroker::reset(
            $validated,
            function (User $user) use ($validated) {
                $user->forceFill([
                    'password' => Hash::make($validated['password']),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === PasswordBroker::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route('profil')->with('success', 'Password berhasil diperbarui.');
    }

    public function updateProfileData(Request $request)
    {
        $validated = $request->validate([
            'angkatan' => ['required', 'integer', 'min:1980', 'max:' . (now()->year + 1)],
            'program_studi' => 'required|string|max:150',
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
        ], [
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, spasi, tanda +, - atau tanda kurung.',
        ]);

        $request->user()->update($validated);

        return redirect()->route('profil')->with('success', 'Data diri berhasil diperbarui.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $validated = $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = $request->user();
        $fotoPath = $validated['foto']->store('profile-photos/' . $user->id, 'public');

        if ($fotoPath === false) {
            throw new \RuntimeException('Foto profil gagal disimpan.');
        }

        $oldFotoPath = $user->foto_path;
        $user->update(['foto_path' => $fotoPath]);

        if ($oldFotoPath && Storage::disk('public')->exists($oldFotoPath)) {
            if (!Storage::disk('public')->delete($oldFotoPath)) {
                Log::warning('Old profile photo could not be deleted.', ['user_id' => $user->id]);
            }
        }

        return redirect()->route('profil')->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:50|unique:users,nim',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'name.required' => 'Nama wajib diisi',
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil, silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('logout.success');
    }
}