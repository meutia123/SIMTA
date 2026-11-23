<?php

namespace App\Http\Controllers;

use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenDashboardController extends Controller
{
    public function index()
    {
        $dosenId = Auth::id();

        $mahasiswaSaya = TugasAkhir::query()
            ->where(function ($query) use ($dosenId) {
                $query->where('calon_dosen_1', $dosenId)
                    ->orWhere('calon_dosen_2', $dosenId)
                    ->orWhere('calon_dosen_3', $dosenId);
            })
            ->distinct('user_id')
            ->count('user_id');

        $mahasiswaTerbaru = TugasAkhir::with('mahasiswa')
            ->where(function ($query) use ($dosenId) {
                $query->where('calon_dosen_1', $dosenId)
                    ->orWhere('calon_dosen_2', $dosenId)
                    ->orWhere('calon_dosen_3', $dosenId);
            })
            ->latest()
            ->take(5)
            ->get();

        $pengujiSempro = max(0, (int) floor($mahasiswaSaya * 0.6));
        $pengujiSidang = max(0, (int) floor($mahasiswaSaya * 0.4));

        return view('pages.dosen.dashboard', [
            'mahasiswaSaya' => $mahasiswaSaya,
            'pengujiSempro' => $pengujiSempro,
            'pengujiSidang' => $pengujiSidang,
            'jadwalHariIni' => 1,
            'mahasiswaTerbaru' => $mahasiswaTerbaru,
        ]);
    }
}
