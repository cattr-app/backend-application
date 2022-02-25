<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRequestClient
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        foreach (config('auth.clients') as $key => $value) {
            if (preg_match("/$value/", $request->header('user-agent'))) {
                $request->attributes->set('clientType', $key);
                $request->attributes->set('client', $request->header('user-agent'));
            }
        }

        return $next($request);
    }
}
