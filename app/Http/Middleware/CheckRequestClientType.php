<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRequestClientType
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
        $client = null;

        foreach (config('auth.clients') as $key => $value) {
            if (preg_match("/$value/", $request->header('user-agent'))) {
                $client = $key;
            }
        }

        $request->attributes->set('clientType', $client);

        return $next($request);
    }
}
