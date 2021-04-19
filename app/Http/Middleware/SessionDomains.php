<?php

namespace App\Http\Middleware;

use Closure;

class SessionDomains
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
        if ($request->getHost() === 'nifty-perlman-4bea8a.netlify.app') {
            config([
                'session.domain' => '.nifty-perlman-4bea8a.netlify.app'
            ]);
        }
        return $next($request);
    }
}
