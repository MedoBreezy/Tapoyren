<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class Instructor
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
        $bearer = $request->header('Authorization');
        $bearer = str_replace('Bearer ', '', $bearer);

        $error = response()->json(['error' => 'Unauthenticated'], 401);

        if ($bearer !== '') {
            $check = User::where('type', 'instructor')->where('api_token', $bearer)->count() === 1;
            if ($check) return $next($request);
            else return $error;
        } else if (auth()->check() && auth()->user()->type === 'instructor') return $next($request);
        else return $error;
    }
}
