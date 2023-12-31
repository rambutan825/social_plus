<?php

declare(strict_types=1);

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2016-Present ZhiYiChuangXiang Technology Co., Ltd.     |
 * +----------------------------------------------------------------------+
 * | This source file is subject to enterprise private license, that is   |
 * | bundled with this package in the file LICENSE, and is available      |
 * | through the world-wide-web at the following url:                     |
 * | https://github.com/slimkit/plus/blob/master/LICENSE                  |
 * +----------------------------------------------------------------------+
 * | Author: Slim Kit Group <master@zhiyicx.com>                          |
 * | Homepage: www.thinksns.com                                           |
 * +----------------------------------------------------------------------+
 */

namespace Zhiyi\Plus\Http\Controllers\APIs\V2;

use Illuminate\Contracts\Routing\ResponseFactory as ContractResponse;
use Illuminate\Support\Arr;
use Zhiyi\Plus\Http\Requests\API2\StoreWalletRecharge;

class WalletRechargeAlipayController extends WalletRechargeController
{
    /**
     * The charge model instance key.
     *
     * @var string
     */
    protected $modelSingletonKey = 'wallet.charge.model';

    /**
     * Create a Alipay recharge charge.
     *
     * @param  \Zhiyi\Plus\Http\Requests\API2\StoreWalletRecharge  $request
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function create(StoreWalletRecharge $request)
    {
        $model = $this->createChargeModel($request, $type = $request->input('type'));

        // 设置共享模型
        $this->app->singleton($this->modelSingletonKey, function () use ($model) {
            return $model;
        });

        return $this->resolveStore($type);
    }

    /**
     * Create a APP rechrage by Alipay.
     *
     * @param  \Illuminate\Contracts\Routing\ResponseFactory  $response
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function alipayStore(ContractResponse $response)
    {
        $model = $this->app->make($this->modelSingletonKey);
        $charge = $this->createCharge($model);

        $model->charge_id = $charge['id'];
        $model->saveOrFail();

        return $response
            ->json(['id' => $model->id, 'charge' => $charge])
            ->setStatusCode(201);
    }

    /**
     * Create a wap recharge by Alipay.
     *
     * @param  \Zhiyi\Plus\Http\Requests\API2\StoreWalletRecharge  $request
     * @param  \Illuminate\Contracts\Routing\ResponseFactory  $response
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function alipayWapStore(StoreWalletRecharge $request, ContractResponse $response)
    {
        $extra = $request->input('extra');

        if (! is_array($extra)) {
            $this->app->abort(422, '请求参数不合法');
        } elseif (! Arr::get($extra, 'success_url')) {
            $this->app->abort(422, 'extra.success_url 必须存在');
        }

        $model = $this->app->make($this->modelSingletonKey);
        $charge = $this->createCharge($model, $extra);

        $model->charge_id = $charge['id'];
        $model->saveOrFail();

        return $response
            ->json(['id' => $model->id, 'charge' => $charge])
            ->setStatusCode(201);
    }

    /**
     * Create a PC recharge by Alipay.
     *
     * @param  \Zhiyi\Plus\Http\Requests\API2\StoreWalletRecharge  $request
     * @param  \Illuminate\Contracts\Routing\ResponseFactory  $response
     * @return mixed
     *
     * @author bs <414606094@qq.com>
     */
    public function alipayPcDirectStore(StoreWalletRecharge $request, ContractResponse $response)
    {
        $extra = $request->input('extra');

        if (! is_array($extra)) {
            $this->app->abort(422, '请求参数不合法');
        } elseif (! Arr::get($extra, 'success_url')) {
            $this->app->abort(422, 'extra.success_url 必须存在');
        }

        $model = $this->app->make($this->modelSingletonKey);
        $charge = $this->createCharge($model, $extra);

        $model->charge_id = $charge['id'];
        $model->saveOrFail();

        return $response
            ->json(['id' => $model->id, 'charge' => $charge])
            ->setStatusCode(201);
    }
}
