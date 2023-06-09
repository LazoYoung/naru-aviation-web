<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerifyAdmin {
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed {
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
