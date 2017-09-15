<?php

namespace Zhiyi\Plus\Http\Controllers\Admin;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Zhiyi\Plus\Models\Reward;
use Zhiyi\Plus\Http\Controllers\Controller;

class RewardController extends Controller
{

    private $rewardableTypes = [
       'feeds' => '动态打赏',
       'news'  => '咨询打赏',
       'users' => '用户打赏',
       'question-answers' => '问答打赏',
    ];

    /**
     * 打赏日期分组统计.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        $items = $this->byConditionsGetStatisticsData($request);

        return response()->json($items, 200);
    }

    /**
     * 根据条件获取统计数据.
     *
     * @param Request $request
     * @return mixed
     */
    protected function byConditionsGetStatisticsData(Request $request)
    {
        $type = $request->get('type');
        $start = $request->get('start');
        $scope = $request->get('scope');
        $end = $request->get('end');

        if ($scope) {
            if ($scope == 'today') {
                $start = Carbon::now()->startOfDay()->toDateTimeString();
                $end = Carbon::now()->endOfDay()->toDateTimeString();
            } elseif ($scope == 'week') {
                $start = Carbon::now()->addDay(-7)->startOfDay()->toDateTimeString();
                $end = Carbon::now()->toDateTimeString();
            }
        } else {
            if ($start && $end) {
                $start = Carbon::parse($start)->startOfDay()->toDateTimeString();
                $end = Carbon::parse($end)->endOfDay()->toDateTimeString();
            }
        }

        $items = Reward::select(DB::raw(
            'count(*) AS reward_count, 
             sum(amount) AS reward_amount, 
             LEFT (created_at, 10) AS reward_date'
        ))
        ->when($type, function ($query) use ($type) {
            $query->where('rewardable_type', $type);
        })
        ->when($start && $end, function ($qeury) use ($start, $end) {
            $qeury->whereBetween('created_at', [$start, $end]);
        })
        ->groupBy('reward_date')
        ->orderBy('reward_date', 'asc')
        ->get()
        ->toArray();

        return $items;
    }

    /**
     * 打赏清单.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rewards(Request $request)
    {
        $type = $request->get('type');
        $start = $request->get('start');
        $end = $request->get('end');
        $keyword = $request->get('keyword');
        $perPage = (int) $request->get('perPage', 20);

        $items = Reward::with(['user', 'target'])
        ->when($type, function ($query) use ($type) {
            $query->where('rewardable_type', $type);
        })
        ->when($start && $end, function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [
                Carbon::parse($start)->startOfDay()->toDateTimeString(),
                Carbon::parse($end)->endOfDay()->toDateTimeString(),
            ]);
        })
        ->when($keyword, function ($query) use ($keyword) {
            if (is_numeric($keyword)) {
                $query->where('user_id', $keyword);
            } else {
                $query->whereHas('user', function ($qeruy) use ($keyword) {
                    $qeruy->where('name', 'like', $keyword);
                });
            }
        })
        ->orderBy('id', 'desc')
        ->paginate($perPage);

        return response()->json($items, 200);
    }

    /**
     * 导出下载.
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        if ($request->get('export_type') === 'statistic') {
            $title = ['打赏次数', '打赏金额（元）', '打赏日期'];
            $data = $this->byConditionsGetStatisticsData($request);
            $this->exportExcel($data, $title, '打赏统计');
        } else {
            $title = ['打赏用户', '被打赏用户', '金额元', '打赏应用', '打赏时间'];
            $items = $this->byConditionsGetRewardData($request);
            $data = $this->convertRewardData($items);
            $this->exportExcel($data, $title, '打赏详情');
        }
    }

    /**
     * 根据条件获取打赏数据.
     *
     * @param Request $request
     * @return mixed
     */
    protected function byConditionsGetRewardData(Request $request)
    {
        $type = $request->get('type');
        $start = $request->get('start');
        $end = $request->get('end');
        $keyword = $request->get('keyword');

        $items = Reward::with(['user', 'target'])
        ->when($type, function ($query) use ($type) {
            $query->where('rewardable_type', $type);
        })
        ->when($start && $end, function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [
                Carbon::parse($start)->startOfDay()->toDateTimeString(),
                Carbon::parse($end)->endOfDay()->toDateTimeString(),
            ]);
        })
        ->when($keyword, function ($query) use ($keyword) {
            if (is_numeric($keyword)) {
                $query->where('user_id', $keyword);
            } else {
                $query->whereHas('user', function ($qeruy) use ($keyword) {
                    $qeruy->where('name', 'like', $keyword);
                });
            }
        })
        ->orderBy('id', 'desc')
        ->get()
        ->toArray();

        return $items;
    }

    /**
     * 转换打赏数组.
     *
     * @param array $data
     * @return array
     */
    public function convertRewardData(array $data)
    {
      $items = [];
      foreach ($data as $key => $value) {
          $items[$key][] = $value['user']['name'];
          $items[$key][] = $value['target']['name'];
          $items[$key][] = $value['amount']/100;
          $items[$key][] = $this->rewardableTypes[$value['rewardable_type']];
          $items[$key][] = $value['created_at'];
      }
      return $items;
    }


    /**
     * export excel.
     *
     * @param array $data  数据
     * @param array $title 列名
     * @param string $filename
     */
    public function exportExcel(array $data = [], array $title = [], $filename = 'export') {
        //set response header
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header(sprintf('Content-Disposition:attachment;filename=%s.xls', $filename));
        header("Pragma: no-cache");
        header("Expires: 0");

        if (count($title)) {
            foreach ($title as $k => $v) {
                $title[$k]=iconv("UTF-8", "GB2312", $v);
            }
            $title = implode("\t", $title);
            echo "$title\n";
        }

        if (count($data)) {
            foreach ($data as $key => $val){
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
                }
                $data[$key] = implode("\t", $data[$key]);
            }
            echo implode("\n", $data);
        }

    }

}
