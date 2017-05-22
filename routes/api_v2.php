<?php


// 应用启动配置
Route::get('/bootstrappers', 'BootstrappersController@show');

// 用户登录
Route::post('/login', 'LoginController@store');

// 创建注册验证码
Route::post('/verifycodes/register', 'VerifyCodeController@storeByRegister');
// 已存在用户发送验证码
Route::post('/verifycodes', 'VerifyCodeController@store');

// 当前用户资料接口
Route::prefix('/user')
->middleware('auth:api')
->group(function () {
    // 当前用户资料
    Route::get('/', 'CurrentUserController@show');
});

// 用户相关
Route::prefix('/users')
->group(function () {
    // 获取用户列表
    Route::get('/', 'UserController@show');
    // 获取单用户
    Route::get('/{user}', 'UserController@user');
});
