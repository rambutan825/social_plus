<?php

namespace Zhiyi\Plus\Http\Controllers\Admin;

use Closure;
use Carbon\Carbon;
use Zhiyi\Plus\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Zhiyi\Plus\Models\CommonConfig;
use Illuminate\Support\Facades\Cache;
use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Zhiyi\Plus\Support\Configuration;

class SiteController extends Controller
{
    /**
     * The store CommonConfig instance.
     *
     * @var Zhiyi\Plus\Models\CommonConfig
     */
    protected $commonCinfigModel;

    protected $app;

    /**
     * Construct handle.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the website info.
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function get(Request $request, Repository $config, ResponseFactory $response)
    {
        if (! $request->user()->can('admin:site:base')) {
            return response()->json([
                'message' => '没有权限查看该项信息',
            ])->setStatusCode(403);
        }

        $name = $config->get('app.name', 'ThinkSNS+');
        $keywords = $config->get('app.keywords');
        $description = $config->get('app.description');
        $icp = $config->get('app.description');

        return $response->json([
            'name' => $name,
            'keywords' => $keywords,
            'description' => $description,
            'icp' => $icp,
        ])->setStatusCode(200);

        // $sites = $this->newCommonConfigModel()
        //     ->byNamespace('site')
        //     ->whereIn('name', ['title', 'keywords', 'description', 'icp'])
        //     ->get();

        // $sites = $sites->mapWithKeys(function ($item) {
        //     return [$item['name'] => $item['value']];
        // });

        // return response()->json($sites)->setStatusCode(200);
    }

    /**
     * 更新网站基本信息.
     *
     * @param Request $request
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function updateSiteInfo(Request $request, Configuration $config, ResponseFactory $response)
    {
        if (! $request->user()->can('admin:site:base')) {
            return response()->json([
                'message' => '没有权限更新该信息',
            ])->setStatusCode(403);
        }

        $keys = ['name', 'keywords', 'description', 'icp'];
        // $requestSites = array_filter($request->only($keys));

        $site = [];
        foreach (array_filter($request->only($keys)) as $key => $value) {
            $site['app.'.$key] = $value;
        }
        $config->set($site);

        return $response->json([
            'message' => '更新成功',
        ])->setStatusCode(201);

        // $sites = $this->newCommonConfigModel()
        //     ->byNamespace('site')
        //     ->whereIn('name', $keys)
        //     ->get()
        //     ->keyBy('name');

        // $callback = function () use ($sites, $requestSites) {
        //     foreach ($requestSites as $name => $value) {
        //         $model = $sites[$name] ?? false;
        //         if (! $model) {
        //             $model = new CommonConfig();
        //             $model->namespace = 'site';
        //             $model->name = $name;
        //             $model->value = $value;
        //             $model->save();
        //             continue;
        //         }

        //         $this->newCommonConfigModel()
        //             ->byNamespace('site')
        //             ->byName($name)
        //             ->update([
        //                 'value' => $value,
        //             ]);
        //     }

        //     return response()->json([
        //         'message' => '更新成功',
        //     ])->setStatusCode(201);
        // };
        // $callback->bindTo($this);

        // return $this->dbTransaction($callback);
    }

    /**
     * 获取全部地区.
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function areas(Request $request)
    {
        if (! $request->user()->can('admin:area:show')) {
            return response()->json([
                'message' => '你没有权限查看地区数据',
            ])->setStatusCode(403);
        }

        $expiresAt = Carbon::now()->addMonth(12);
        $areas = Cache::remember('areas', $expiresAt, function () {
            return Area::all();
        });

        return response()->json($areas ?: [])->setStatusCode(200);
    }

    /**
     * 添加地区.
     *
     * @param Request $request
     *
     * @return mixed [description]
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function doAddArea(Request $request)
    {
        if (! $request->user()->can('admin:area:add')) {
            return response()->json([
                'error' => ['你没有添加地区权限'],
            ])->setStatusCode(403);
        }

        $name = $request->input('name');
        $extends = $request->input('extends', '');
        $pid = $request->input('pid', 0);

        if (! $name) {
            return response()->json([
                'error' => ['name' => '名称不能为空'],
            ])->setStatusCode(422);
        } elseif ($pid && ! Area::find($pid)) {
            return response()->json([
                'error' => ['pid' => '父地区不存在'],
            ])->setStatusCode(422);
        }

        $area = new Area();
        $area->name = $name;
        $area->extends = $extends;
        $area->pid = $pid;
        if (! $area->save()) {
            return response()->json([
                'error' => ['数据库保存失败'],
            ])->setStatusCode(500);
        }

        Cache::forget('areas');

        return response()->json($area)->setStatusCode(201);
    }

    /**
     * 删除地区.
     *
     * @param int $id
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function deleteArea(Request $request, int $id)
    {
        if (! $request->user()->can('admin:area:delete')) {
            return response()->json([
                'error' => ['你没有权限删除地区'],
            ])->setStatusCode(403);
        }

        $notEmpty = Area::byPid($id)->first();
        if ($notEmpty) {
            return response()->json([
                'error' => '请先删除该地区下级地区',
            ])->setStatusCode(422);
        }

        Area::where('id', $id)->delete();
        Cache::forget('areas');

        return response('', 204);
    }

    /**
     * 更新地区数据.
     *
     * @param Request $request
     * @param Area    $area
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function patchArea(Request $request, Area $area)
    {
        if (! $request->user()->can('admin:area:update')) {
            return response()->json([
                'error' => ['你没有更新地区权限'],
            ])->setStatusCode(403);
        }

        $key = $request->input('key');
        $value = $request->input('value', '');

        if (! in_array($key, ['name', 'extends'])) {
            return response()->json([
                'error' => ['请求不合法'],
            ])->setStatusCode(422);
        } elseif ($key == 'name' && ! $value) {
            return response()->json([
                'error' => ['name' => '地区名称不能为空'],
            ])->setStatusCode(422);
        }

        $area->$key = $value;
        if (! $area->save()) {
            return response()->json([
                'error' => ['数据更新失败'],
            ])->setStatusCode(500);
        }

        Cache::forget('areas');

        return response()->json([
            'message' => [$key => '更新成功'],
        ])->setStatusCode(201);
    }

    /**
     * instance a new model.
     *
     * @return Zhiyi\Plus\Models\CommonConfig::newQuery
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    protected function newCommonConfigModel()
    {
        if (! $this->commonCinfigModel instanceof CommonConfig) {
            $this->commonCinfigModel = new CommonConfig();
        }

        return $this->commonCinfigModel->newQuery();
    }

    /**
     * Static bind DB::transaction .
     *
     * @param Closure $callback
     * @param int     $attempts
     *
     * @return mixed
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    protected function dbTransaction(Closure $callback, $attempts = 1)
    {
        return DB::transaction($callback, $attempts);
    }
}
