<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    // Menampilkan daftar mahasiswa
    public function index()
    {
        $mahasiswa = User::where('role', 'mahasiswa')
            ->latest()
            ->get();

        return view('prodi.mahasiswa', compact('mahasiswa'));
    }

    // Menambahkan mahasiswa sekaligus membuat akun login
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:50|unique:users,nim',
            'email' => 'required|email|max:255|unique:users,email',
        ], [
            'name.required' => 'Nama mahasiswa wajib diisi.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        // Password awal mahasiswa
        $passwordAwal = 'mhs123';

        User::create([
            'name' => $validated['name'],
            'nim' => $validated['nim'],
            'email' => $validated['email'],
            'password' => Hash::make($passwordAwal),
            'role' => 'mahasiswa',
        ]);

        return redirect()
            ->route('prodi.mahasiswa')
            ->with('success', 'Mahasiswa berhasil ditambahkan. Password awal: mhs123');
    }
}