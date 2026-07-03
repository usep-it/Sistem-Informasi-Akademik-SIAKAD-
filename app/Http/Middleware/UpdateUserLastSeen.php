<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UpdateUserLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
{
    // Skip update untuk logout route agar tidak ada race condition
    // Check dengan 3 cara untuk memastikan route logout ter-skip
    $isLogoutRoute = $request->is('logout') 
                   || $request->is('logout/*')
                   || $request->getMethod() === 'POST' && $request->is('logout');
    
    if ($isLogoutRoute || $request->route()?->getName() === 'logout') {
        return $next($request);
    }

    if (Auth::check()) {
        $user = Auth::user();
        $now = now('Asia/Jakarta');

        // Gunakan variabel $now agar sinkron
        if (!$user->last_seen_at || \Carbon\Carbon::parse($user->last_seen_at)->diffInMinutes($now) >= 1) {
            $user->timestamps = false; 
            $user->last_seen_at = $now->toDateTimeString();
            $user->ip_address = $request->ip();
            $user->save();
        }
    }
    return $next($request);
}
}