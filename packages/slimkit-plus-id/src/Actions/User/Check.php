<?php

/*
 * declare(strict_types=1);
 *
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

namespace SlimKit\PlusID\Actions\User;

use SlimKit\PlusID\Actions\Action;
use SlimKit\PlusID\Support\Message;
use Zhiyi\Plus\Models\User as UserModel;

class Check extends Action
{
    public function getSignAction(): array
    {
        return [
            'app' => $this->client->id,
            'action' => 'user/check',
            'time' => (int) $this->request->time,
        ];
    }

    public function dispatch()
    {
        $map = $this->request->only(['phone', 'name', 'email']);
        $map['id'] = $this->request->user;
        foreach ($map as $key => &$value) {
            if (! $value) {
                $value = null;
                continue;
            }

            $value = (bool) UserModel::where($key, $value)->first();
        }

        return $this->response(new Message(200, 'success', $map));
    }
}
