<?php

use Illuminate\Http\Request;

// 获取手机验证码
Route::post('/auth/phone/send-code', 'AuthController@sendPhoneCode')
    ->middleware(App\Http\Middleware\VerifyPhoneNumber::class) // 验证手机号格式是否正确
    ->middleware(App\Http\Middleware\VerifySendPhoneCodeType::class) // 验证发送验证码类型等集合
;

// 用户登录
Route::post('/auth', 'AuthController@login')
    ->middleware(App\Http\Middleware\CheckDeviceCodeExisted::class) // 验证设备号是否存在
    ->middleware(App\Http\Middleware\VerifyPhoneNumber::class) // 验证手机号码是否正确
    ->middleware(App\Http\Middleware\CheckUserByPhoneExisted::class) // 验证手机号码用户是否存在
    ->middleware(App\Http\Middleware\VerifyPassword::class) // 验证密码是否正确
;

// 重置token接口
Route::patch('/auth', 'AuthController@resetToken');

// 用户注册
Route::post('/auth/register', 'AuthController@register')
    ->middleware(App\Http\Middleware\CheckDeviceCodeExisted::class) // 验证设备号是否存在
    ->middleware(App\Http\Middleware\VerifyPhoneNumber::class) // 验证手机号码是否正确
    ->middleware(App\Http\Middleware\VerifyUserNameRole::class) // 验证用户名规则是否正确
    ->middleware(App\Http\Middleware\CheckUserByNameNotExisted::class) // 验证用户名是否被占用
    ->middleware(App\Http\Middleware\CheckUserByPhoneNotExisted::class) // 验证手机号码是否被占用
    ->middleware(App\Http\Middleware\VerifyPhoneCode::class) // 验证验证码释放正确
;

// 找回密码
Route::patch('/auth/forgot', 'AuthController@forgotPassword')
    ->middleware(App\Http\Middleware\VerifyPhoneNumber::class) // 验证手机号格式
    ->middleware(App\Http\Middleware\CheckUserByPhoneExisted::class) // 验证手机号码用户是否存在
    ->middleware(App\Http\Middleware\VerifyPhoneCode::class) // 验证手机号码验证码是否正确
;

// 用户相关组
Route::group([
    'middleware' => [
        App\Http\Middleware\AuthUserToken::class,
    ],
    'prefix' => 'users',
], function ($routes) {
    Route::patch('/', 'UserController@profile');
    // 修改用户密码
    Route::patch('/password', 'UserController@resetPassword') // 设置控制器
        ->middleware(App\Http\Middleware\VerifyPassword::class); // 验证用户密码是否正确
    Route::get('/{user}', 'UserController@get');
        ->middleware(App\Http\Middleware\VerifyPassword::class) // 验证用户密码是否正确
;
});

// 获取一个附件资源
Route::get('/storages/{storage}', 'StorageController@get');
// 附件储存相关
Route::group([
    'middleware' => [
        App\Http\Middleware\AuthUserToken::class,
    ],
    'prefix' => 'storages',
], function () {
    // 创建一个储存任务
    Route::post('/task/{hash}/{origin_filename}', 'StorageController@create');
    // 完成一个任务上传通知
    Route::patch('/task/{storage_task_id}', 'StorageController@notice');
    // 删除一个上传任务附件
    Route::delete('/task/{storage_task_id}', 'StorageController@delete');
    // local storage api.
    Route::post('/task/{storage_task_id}', 'StorageController@upload')
        ->name('storage/upload');
});

Route::post('/storage', function (Request $request) {
    // var_dump(app());
    $hash = $request->input('hash');
    $filename = $request->input('filename');
    $storage = new Ts\Storages\Storage();
    $info = $storage->createStorageTask($filename, $hash);
    var_dump($info);
});
