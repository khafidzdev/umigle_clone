<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Jika user belum melengkapi profil dan tidak sedang berada di halaman setup profil
            if (!Auth::user()->profile_completed && !$request->is('profile/setup*')) {
                return redirect()->route('profile.setup');
            }
        }

        return $next($request);
    }
}