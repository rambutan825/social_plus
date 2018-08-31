<?php

declare(strict_types=1);

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2018 Chengdu ZhiYiChuangXiang Technology Co., Ltd.     |
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

namespace Zhiyi\Plus\FileStorage\Providers;

use Zhiyi\Plus\AppInterface;
use Zhiyi\Plus\FileStorage\Storage;
use Illuminate\Support\ServiceProvider;
use Zhiyi\Plus\FileStorage\ChannelManager;
use Zhiyi\Plus\FileStorage\Http\MakeRoutes;
use Zhiyi\Plus\FileStorage\StorageInterface;
use Zhiyi\Plus\FileStorage\FilesystemManager;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register StorageInterface instance.
        $this->app->singleton(StorageInterface::class, function (AppInterface $app) {
            $manager = $this->app->make(ChannelManager::class);

            return new Storage($app, $manager);
        });

        // // Register FilesystemManager instance.
        // $this->app->singleton(FilesystemManager::class, function (AppInterface $app) {
        //     return new FilesystemManager($app);
        // });

        // // Register ChannelManager instance.
        // $this->app->singleton(ChannelManager::class, function (AppInterface $app) {
        //     $manager = $this->app->make(FilesystemManager::class);

        //     return new ChannelManager($app, $manager);
        // });
    }

    public function boot()
    {
        $this
            ->app
            ->make(MakeRoutes::class)
            ->register();
    }
}
