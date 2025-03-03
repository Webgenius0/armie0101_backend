<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BeautyExpertMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'beauty_expert') {
                if ($user->status === 'inactive') {
                    return redirect()->route('profile-submitted');
                }
                return $next($request);
            } elseif ($user->role === 'admin') {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('client-dashboard');
            }
        }

        return redirect()->route('login');
    }
}
