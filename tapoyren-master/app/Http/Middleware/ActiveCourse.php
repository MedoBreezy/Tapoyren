<?php

namespace App\Http\Middleware;

use Closure;

class ActiveCourse
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

        if(isset($parameters['course']) && $parameters['course']->status==='active') return $next($request);
        else abort(404);
    }
}
