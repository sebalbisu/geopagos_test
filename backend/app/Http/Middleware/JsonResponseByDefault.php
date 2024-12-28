<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonResponseByDefault
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (! $response->headers->has('Content-Type')) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
