<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/admin')->with('error', 'Please log in as admin.');
        }

        if ((int) Auth::user()->role_id !== 5) {
            Auth::logout();

            return redirect('/admin')->with('error', 'You do not have permission.');
        }

        return $next($request);
    }
}
