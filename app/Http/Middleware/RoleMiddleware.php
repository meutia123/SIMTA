<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $userRole = strtolower(trim((string) $user->role));
        if ($userRole === strtolower(trim($role))) {
            return $next($request);
        }

        $dashboard = [
            'mahasiswa' => 'dashboard',
            'dosen' => 'dosen.dashboard',
            'prodi' => 'prodi.dashboard',
            'tu' => 'tu.dashboard',
        ][$userRole] ?? null;

        if ($dashboard) {
            return redirect()->route($dashboard);
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');

    }
}