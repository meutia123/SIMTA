<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DosenDashboardController extends Controller
{
    public function index()
    {
        return view('dosen.dashboard', [
            'mahasiswaSaya' => 5,
            'pengujiSempro' => 2,
            'pengujiSidang' => 1,
            'jadwalHariIni' => 1,
            'mahasiswaTerbaru' => collect(),
        ]);
    }
}