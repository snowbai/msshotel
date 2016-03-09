<?php

namespace common\models\order;

use common\models\order\order_lists;

/**
 * 订单列表类
 * Class OrderLister
 * @package common\models\order
 */
class OrderLister //工厂
{
    /**
     * 获取订单列表对象
     * @param $group_id
     * @param $hotel_id
     * @param $product_type
     * @param $product_subtype
     * @return order_lists\OrderList|OrderListMulti|null
     */
    public static function getLister($group_id, $hotel_id, $product_type=null, $product_subtype=null)
    {
        if($product_type===null){
            return self::_getAllList($group_id, $hotel_id);
        } else {
            return order_lists\OrderListFactory::getInstance($group_id, $hotel_id, $product_type, $product_subtype);
        }
    }

    /**
     * 获取所有订单的列表对象
     * @param $group_id
     * @param $hotel_id
     * @return OrderListMulti
     */
    protected static function _getAllList($group_id, $hotel_id)
    {
        $lister = new order_lists\OrderListMulti($group_id, $hotel_id);
        $lister->addLister(new order_lists\RoomOrderList($group_id,$hotel_id));
        $lister->addLister(new order_lists\PromotionOrderList($group_id,$hotel_id));
        $lister->addLister(new order_lists\DinnerOrderList($group_id,$hotel_id));
        return $lister;
    }
}
