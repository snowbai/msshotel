<?php

namespace common\models\order\order_lists;

/**
 * 订单列表接口类
 * Class OrderList
 * @package common\models\order\order_lists
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
interface IOrderList
{
    /**
     * 获取类型
     * @return mixed
     */
    public static function getType();

    /**
     * 获取待处理订单列表
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return mixed
     */
    public function getPendingList($search_data, $offset, $limit, $get_count=false, $with_product_type=false);

    /**
     * 获取无效订单列表
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return mixed
     */
    public function getInvalidList($search_data, $offset, $limit, $get_count=false, $with_product_type=false);

    /**
     * 获取订单列表
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param $with_product_type
     * @return mixed
     */
    public function getList($search_data, $offset, $limit, $get_count=false, $with_product_type=false);
}