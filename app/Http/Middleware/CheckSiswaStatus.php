<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSiswaStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'Siswa' || !$user->siswa) {
            return $next($request);
        }

        $status = $user->siswa->status;

        if ($status === 'Tidak Aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Login Ditolak: Akun Anda sudah dinonaktifkan.');
        }

        if ($status !== 'Aktif') {
            $allowedRoutes = [
                'home',
                'alumni.dashboard',
                'nilai.saya',
                'laporan.pdf',
                'profil.edit',
                'profil.update',
                'logout',
            ];

            $routeName = optional($request->route())->getName();

            if (!in_array($routeName, $allowedRoutes)) {
                return redirect()->route('home')
                    ->with('error', 'Akses terbatas. Siswa alumni hanya dapat melihat laporan nilai dan mencetak transkrip.');
            }
        }

        return $next($request);
    }
}
