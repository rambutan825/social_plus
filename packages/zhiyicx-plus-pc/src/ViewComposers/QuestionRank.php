<?php

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

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentPc\ViewComposers;

use Illuminate\View\View;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentPc\api;

class QuestionRank
{
    public function compose(View $view)
    {
        $qrank['day'] = api('GET', '/api/v2/question-ranks/answers', ['limit' => 5, 'type' => 'day']);
        $qrank['week'] = api('GET', '/api/v2/question-ranks/answers', ['limit' => 5, 'type' => 'week']);
        $qrank['month'] = api('GET', '/api/v2/question-ranks/answers', ['limit' => 5, 'type' => 'month']);

        $view->with('qrank', $qrank);
    }
}
