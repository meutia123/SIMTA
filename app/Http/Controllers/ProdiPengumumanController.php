<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\User;
use App\Notifications\MahasiswaNotification;
use Illuminate\Http\Request;

class ProdiPengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::with('pembuat')->latest()->get();

        return view('prodi.pengumuman', compact('pengumuman'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string|max:10000',
        ]);

        $pengumuman = Pengumuman::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        User::where('role', 'mahasiswa')->chunkById(100, function ($mahasiswa) use ($pengumuman) {
            foreach ($mahasiswa as $user) {
                $user->notify(new MahasiswaNotification(
                    'Pengumuman baru: ' . $pengumuman->judul,
                    route('pengumuman')
                ));
            }
        });

        return redirect()->route('prodi.pengumuman')->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return redirect()->route('prodi.pengumuman')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
