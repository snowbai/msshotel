<?php

namespace common\models\order\order_lists;
use common\models\order\OrderConst;

/**
 * 订单列表工厂类
 * Class OrderListFactory
 * @package common\models\order\order_lists
 */
class OrderListFactory //工厂
{
    /**
     * 获取特定类型的订单列表对象
     * @param $group_id
     * @param $hotel_id
     * @param $product_type
     * @param $product_subtype
     * @return OrderList|null
     */
    public static function getInstance($group_id, $hotel_id, $product_type, $product_subtype)
    {
        switch($product_type) {
            case OrderConst::ORDER_ROOM:
                return new RoomOrderList($group_id, $hotel_id, $product_subtype);
                break;
            case OrderConst::ORDER_PROMOTION:
                return new PromotionOrderList($group_id, $hotel_id, $product_subtype);
                break;
            case OrderConst::ORDER_DINNER:
                return new DinnerOrderList($group_id, $hotel_id, $product_subtype);
                break;
            default:
                return null;
        }
    }
}