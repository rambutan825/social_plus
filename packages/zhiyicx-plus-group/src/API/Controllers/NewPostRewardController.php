<?php

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2017 Chengdu ZhiYiChuangXiang Technology Co., Ltd.     |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 2.0 of the Apache license,    |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available through the world-wide-web at the following url:           |
 * | http://www.apache.org/licenses/LICENSE-2.0.html                      |
 * +----------------------------------------------------------------------+
 * | Author: Slim Kit Group <master@zhiyicx.com>                          |
 * | Homepage: www.thinksns.com                                           |
 * +----------------------------------------------------------------------+
 */

namespace Zhiyi\PlusGroup\API\Controllers;

use Illuminate\Http\Request;
use Zhiyi\Plus\Packages\Wallet\Order;
use Zhiyi\Plus\Packages\Wallet\TypeManager;
use Zhiyi\PlusGroup\Models\Post as GroupPostModel;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class NewPostRewardController
{
    /**
     * 元到分转换比列.
     */
    const RATIO = 100;

    /**
     * 打赏操作.
     *
     * @param Request $request
     * @param GroupPostModel $post
     * @param WalletCharge $charge
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function store(Request $request, GroupPostModel $post, TypeManager $manager, ConfigRepository $config)
    {
        if (! $config->get('plus-group.group_reward.status')) {
            return response()->json(['message' => ['打赏功能已关闭']], 422);
        }

        if ($post->user_id) {
            $amount = (int) $request->input('amount');
        }
        if (! $amount || $amount < 0) {
            return response()->json([
                'amount' => ['请输入正确的打赏金额'],
            ], 422);
        }
        $user = $request->user();

        if ($post->user_id === $user->id) {
            return response()->json(['message' => ['不能打赏自己发布的帖子']], 422);
        }
        $user->load('wallet');
        $post->load('user');
        $target = $post->user;

        if (! $user->newWallet || $user->newWallet->balance < $amount) {
            return response()->json(['message' => ['余额不足']], 403);
        }

        // 记录订单
        $money = $amount / self::RATIO;
        // 1.8启用, 新版未读消息提醒
        $userCount = UserCountModel::firstOrNew([
            'type' => 'user-system',
            'user_id' => $target->id
        ]);
        $userCount->total += 1;

        $status = $manager->driver(Order::TARGET_TYPE_REWARD)->reward([
            'reward_resource' => $post,
            'order' => [
                'user' => $user,
                'target' => $target,
                'amount' => $amount,
                'user_order_body' => sprintf('打赏帖子《%s》，钱包扣除%s元', $post->title, $money),
                'target_order_body' => sprintf('帖子《%s》被打赏，钱包增加%s元', $post->title, $money),
            ],
            'notice' => [
                'type' => 'group:post:reward',
                'detail' => ['user' => $user, 'post' => $post],
                'message' => sprintf('你的帖子《%s》被用户%s打赏%s元', $post->title, $user->name, $money),
            ],
        ]);

        if ($status === true) {
            $userCount->save();
            return response()->json(['message' => ['打赏成功']], 201);
        } else {
            return response()->json(['message' => ['打赏失败']], 500);
        }
    }
}
