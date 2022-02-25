<?php

namespace App\Http\Middleware;

use App\Exceptions\Entities\AuthorizationException;
use App\Models\User;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Lang;

class Authenticate extends BaseAuthenticate
{
    public const DEFAULT_USER_LANGUAGE = 'en';

    public function handle($request, Closure $next, ...$guards): mixed
    {
        $this->authenticate($request, $guards);

        if ($request->attributes->get('clientType') === 'web'
            && session('client') !== $request->attributes->get('client')
        ) {
            $this->logout($request->user());
        }

        if ($request->attributes->get('clientType') !== 'web' && !$request->bearerToken()) {
            $this->logout($request->user());
        }

        if (!optional($request->user())->active) {
            $this->logout($request->user());
        }

        Lang::setLocale(optional($request->user())->user_language ?: self::DEFAULT_USER_LANGUAGE);

        return $next($request);
    }

    private function logout(User $user): void
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        $user->currentAccessToken()?->delete();

        throw new AuthorizationException(AuthorizationException::ERROR_TYPE_UNAUTHORIZED);
    }
}
