<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;

class MahasiswaPengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::with('pembuat')->latest()->paginate(20);

        return view('mahasiswa.pages.pengumuman', compact('pengumuman'));
    }
}
