<?php

namespace common\models\order\orders;

use common\models\ar\Order as OrderAR;
use common\models\order\OrderConst;

/**
 * 订单工厂类
 * Class OrderFactory
 * @package common\models\order\orders
 */
class OrderFactory //工厂
{
    /**
     * 获取特定类型的订单对象
     * @param $order_no
     * @param int $order_id
     * @param null $order_ar
     * @param null $order_search_ar
     * @return PromotionOrder|null
     */
    public static function getInstance($order_no, $order_id=0, $order_ar=null, $order_search_ar=null)
    {
        if($order_id > 0){
            $order_search_ar = OrderAR::findOne(['id'=>intval($order_id)]);
        }elseif(!empty($order_no)){
            $order_search_ar = OrderAR::findOne(['order_no'=>strval($order_no)]);
        }
        if(empty($order_search_ar)) return null;

        $product_type = $order_search_ar->product_type;
        //$product_subtype = $order_search_ar->product_subtype;

        switch($product_type)
        {
            case OrderConst::ORDER_ROOM:
                $order_obj = RoomOrder::getInstance($order_no, $order_id, $order_ar, $order_search_ar);
                break;
            case OrderConst::ORDER_PROMOTION:
                $order_obj = PromotionOrder::getInstance($order_search_ar, $order_id, $order_ar, $order_search_ar);
                break;
            case OrderConst::ORDER_DINNER:
                $order_obj = DinnerOrder::getInstance($order_no, $order_id, $order_ar, $order_search_ar);
                break;
            default:
                $order_obj = null;
        }
        return $order_obj;
    }

}