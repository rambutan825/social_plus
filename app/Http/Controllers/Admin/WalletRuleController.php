<?php

namespace Zhiyi\Plus\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Zhiyi\Plus\Models\CommonConfig;
use Zhiyi\Plus\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;

class WalletRuleController extends Controller
{
    /**
     * θ·εε.
     *
     * εΌγζη°θ§ε.
     *
     * @param ResponseFactory $response
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(ResponseFactory $response)
    {
        $rule = CommonConfig::byNamespace('wallet')
            ->byName('rule')
            ->value('value');

        return $response
            ->json(['rule' => $rule])
            ->setStatusCode(200);
    }

    /**
     * ζ΄ζ°θ§ε.
     *
     * @param Request $request
     * @param ResponseFactory $response
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function update(Request $request, ResponseFactory $response)
    {
        $rule = $request->input('rule', '');

        CommonConfig::updateOrCreate(
            ['name' => 'rule', 'namespace' => 'wallet'],
            ['value' => $rule]
        );

        return $response
            ->json(['message' => ['ζ΄ζ°ζε']])
            ->setStatusCode(201);
    }
}
