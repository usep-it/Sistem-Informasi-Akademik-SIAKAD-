<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HakRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$roles)
{
    $user = auth()->user();

    // Jika role cocok → lanjut
    if (in_array($user->role, $roles)) {
        return $next($request);
    }

    // Jika GURU tapi JABATAN cocok → lanjut
    if ($user->role === 'Guru' && $user->pegawai) {
        $jabatan = $user->pegawai->jabatan;

        if (in_array($jabatan, $roles)) {
            return $next($request);
        }
    }

    // Jika tidak cocok → tolak
    return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
}

}
