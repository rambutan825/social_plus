<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\MessageResponseBody;
use App\Handler\SendMessage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerifyCode;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * 发送注册验证码.
     *
     * @param Request $request 请求对象
     *
     * @return Response 返回对象
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function getPhoneCode(Request $request)
    {
        $vaildSecond = 60;
        $phone = $request->input('phone');
        $verify = VerifyCode::byAccount($phone)->byValid($vaildSecond)->orderByDesc()->first();

        if ($verify) {
            return app(MessageResponseBody::class, [
                'code' => 1008,
                'data' => $verify->makeSurplusSecond($vaildSecond),
            ]);
        }

        $verify = new VerifyCode();
        $verify->account = $phone;
        $verify->makeVerifyCode();
        $verify->save();

        return app(SendMessage::class, [$verify, 'type' => 'phone'])->send();
    }

    /**
     * 注册用户.
     *
     * @param Request $request 请求对象
     *
     * @return Response 返回对象
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function register(Request $request)
    {
        $name = $request->input('name');
        $phone = $request->input('phone');
        $password = $request->input('password', '');
        $user = new User();
        $user->name = $name;
        $user->phone = $phone;
        $user->createPassword($password);
        $user->save();

        $request->attributes->set('user', $user);

        return $this->login($request);
    }

    /**
     * 用户登录.
     *
     * @Author   Wayne[qiaobin@zhiyicx.com]
     * @DateTime 2016-12-27T16:57:18+0800
     *
     * @param Request $request 请求对象
     *
     * @return Response 响应对象
     */
    public function login(Request $request)
    {
        dump($request->attributes->get('user'));
    }
}
