<?php

namespace App\Http\Middleware;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful as Middleware;

class EnsureFrontendRequestsAreStateful extends Middleware
{
    /**
     * @inerhitDoc
     */
    public static function fromFrontend($request): bool
    {
        return $request->attributes->get('clientType') === 'web';
    }
}
