<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class MahasiswaNotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        $notifications = $user->notifications()->latest()->paginate(20);

        return view('mahasiswa.pages.notifikasi', compact('notifications'));
    }
}
