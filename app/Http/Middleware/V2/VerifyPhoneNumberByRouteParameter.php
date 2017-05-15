<?php

namespace Zhiyi\Plus\Http\Middleware\V2;

use Closure;
use Validator;
use Illuminate\Http\Request;

/**
 * 验证手机号码
 */
class VerifyPhoneNumberByRouteParameter
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
        $validator = Validator::make($request->route()->parameters(), [
            'phone' => 'cn_phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => '操作失败',
            ])->setStatusCode(403);
        }

        return $next($request);
    }
}
