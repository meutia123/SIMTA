<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TugasAkhir;

class TugasAkhirController extends Controller
{
    public function index()
    {
        $tugasAkhir = TugasAkhir::latest()->first();

        return view('mahasiswa.tugas-akhir', compact('tugasAkhir'));
    }

    public function store(Request $request)
    {
        TugasAkhir::create([
            'user_id' => auth()->id(),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status' => 'Menunggu'
        ]);

        return redirect('/tugas-akhir');
    }
}