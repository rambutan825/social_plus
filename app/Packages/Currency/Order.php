<?php

declare(strict_types=1);

namespace Zhiyi\Plus\Packages\Currency;

use Zhiyi\Plus\Models\CurrencyOrder as CurrencyOrderModel;

class Order
{
    /**
     *  Target types
     */
    const TARGET_TYPE_COMMON = 'default'; // 默认流程
    const TARGET_TYPE_COMMODITY = 'commodity'; // 商品流程
    const TARGET_TYPE_USER = 'user'; // 用户到用户流程
    const TARGET_TYPE_TASK = 'task'; // 积分任务流程
    const TARGET_TYPE_CHARGE = 'charge'; // 充值流程
    const TARGET_TYPE_CASH = 'cash'; // 提现流程
    const TARGET_TYPE_TRANSFORM = 'transform'; // 货币兑换流程

    /**
     * types.
     */
    const TYPE_INCOME = 1;    // 入账
    const TYPE_EXPENSES = -1; // 支出

    /**
     * state types.
     */
    const STATE_WAIT = 0;    // 等待
    const STATE_SUCCESS = 1; // 成功
    const STATE_FAIL = -1;   // 失败

    /**
     * The order model.
     *
     * @var \Zhiyi\Plus\Models\CurrencyOrder
     */
    protected $order;

    public function __construct($order = null)
    {
        if ($order instanceof CurrencyOrderModel) {
            $this->setOrderModel($order);
        }
    }

    protected function setOrderModel(CurrencyOrderModel $order)
    {
        $this->order = $order;

        return $this;
    }

    protected function getOrderModel(): CurrencyOrderModel
    {
        return $this->order;
    }
}
