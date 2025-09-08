<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
  public function handle($request, Closure $next)
{
    \App\Models\Visit::create([
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    return $next($request);
}

}
