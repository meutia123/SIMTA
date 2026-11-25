<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Jadwal;
use App\Models\Pengumuman;
use App\Models\TugasAkhir;
use Illuminate\Support\Facades\Auth;

class MahasiswaDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        return view('dashboard', [
            'tugasAkhirCount' => TugasAkhir::where('user_id', $userId)->count(),
            'dokumenCount' => Document::where('user_id', $userId)->count(),
            'jadwalCount' => Jadwal::where('user_id', $userId)
                ->where('status', 'terjadwal')
                ->where('waktu', '>=', now())
                ->count(),
            'tugasAkhir' => TugasAkhir::where('user_id', $userId)->latest()->first(),
            'pengumuman' => Pengumuman::latest()->limit(3)->get(),
        ]);
    }
}
