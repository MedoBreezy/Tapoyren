<?php

namespace App\Http\Middleware;

use Closure;

class ActivePackage
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
        $parameters = $request->route()->parameters();

        if (isset($parameters['package']) && $parameters['package']->status === 'active') return $next($request);
        else abort(404);
    }
}
