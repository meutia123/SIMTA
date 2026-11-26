<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class DosenProfileController extends Controller
{
    public function index()
    {
        return view('dosen.profil');
    }

    public function updatePhoto(Request $request)
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

        return redirect()->route('dosen.profil')->with('success', 'Foto profil berhasil diperbarui.');
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

        return redirect()->route('dosen.profil')->with('success', 'Password berhasil diperbarui.');
    }
}
