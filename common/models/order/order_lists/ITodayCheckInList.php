<?php

namespace common\models\order\order_lists;

/**
 * 订单列表接口类
 * Class OrderList
 * @package common\models\order\order_lists
 */
interface ITodayCheckInList
{
    /**
     * 获取今日入住订单
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return mixed
     */
    public function getTodayCheckInList($search_data, $offset, $limit, $get_count=false, $with_product_type=false);
}