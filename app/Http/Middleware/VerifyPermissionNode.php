<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserGroupLink;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminUser;
use App\Exceptions\MessageResponseBody;

class VerifyPermissionNode
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
        $user = $request->attributes->get('user');

        // 查找超级管理员表
        $user_groups = AdminUser::byUserId($user->id)->count();

        // 非超级管理员则查找用户节点
        if (!$user_groups) {
            return app(MessageResponseBody::class, [
                'code' => 1004,
                'message' => '没有进入后台的权限',
            ])->setStatusCode(403);
        }

        return $next($request);
    }
}
