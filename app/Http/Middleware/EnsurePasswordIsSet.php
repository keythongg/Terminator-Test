<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordIsSet
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && is_null(Auth::user()->password)) {
            return redirect()->route('password.create')->with('error', 'Morate postaviti šifru prije nastavka.');
        }

        return $next($request);
    }
}
