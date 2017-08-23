<?php

namespace Zhiyi\Plus\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Zhiyi\Plus\Http\Controllers\Controller;
use Zhiyi\Plus\Models\Certification;
use Zhiyi\Plus\Models\CertificationCategory;

class CertificationCategoryController extends Controller
{
    /**
     * @param Request $request
     * @author: huhao <915664508@qq.com>
     */
    public function certifications(Request $request)
    {
    	$perPage = $request->get('perPage', 20);

        $items = CertificationCategory::paginate($perPage);

        return response()->json($items)->setStatusCode(200);
    }

    public function show($name)
    {
        $item = CertificationCategory::where('name',$name)->first();
        return response()->json($item)->setStatusCode(200);
    }

    public function update(Request $request, $name)
    {
       $rule = ['display_name' => 'required'];
       $msg  = ['display_name.required' => '显示名称必须填写'];

       $this->validate($request, $rule, $msg);

       $model = CertificationCategory::where('name', $name)->first();

       $model->display_name = $request->get('display_name');
       $model->description  = $request->get('description');
       $response = $model->save();

        return response()->json([
            'message' => [
                $response === true ? '更新成功' : '更新失败',
            ],
        ])->setStatusCode($response === true ? 201 : 422);

    }

    private function rule()
    {
        return [
            'display_name' => 'required'
        ];
    }

    private function msg()
    {

    }
}
