<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return $next($request);
            } elseif (Auth::user()->role === 'client') {
                return redirect()->route('client-dashboard');
            } else {
                return redirect()->route('beauty-expert-dashboard');
            }
        }

        return redirect()->route('login');
    }
}
