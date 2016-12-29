<?php

namespace App\Http\Middleware;

use App\Exceptions\MessageResponseBody;
use App\Models\AuthToken;
use Closure;
use Illuminate\Http\Request;

class AuthUserToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $accessToken = $request->headers->get('ACCESS-TOKEN');

        if (!$accessToken) {
            return app(MessageResponseBody::class, [
                'code' => 1014,
            ]);
        }

        $authToken = AuthToken::byToken($accessToken)
            ->orderByDesc()
            ->first();

        return $next($request);
    }
}
