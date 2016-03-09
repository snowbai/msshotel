<?php

namespace common\models\order\products\parser;

/**
 * 客房订单数据处理类
 * Class RoomParser
 * @package common\models\order\products\parser
 */
class RoomParser implements IParser
{
    /**
     * 解析产品数据
     * @param $product_data
     * @param $attr
     * @return mixed
     */
    public static function processProductData($product_data, $attr)
    {
        $apply_num = isset($product_data['apply_num']) ? (int) $product_data['apply_num'] : 1;
        $discount_data = isset($attr['discount_data']) ? $attr['discount_data'] : array();

        $apply_attr['apply_arrive_date'] = isset($product_data['apply_arrive_date']) ? $product_data['apply_arrive_date'] : date('Y-m-d');
        $apply_attr['apply_leave_date'] = isset($product_data['apply_leave_date']) ? $product_data['apply_leave_date'] : date('Y-m-d', strtotime('+1 day'));
        $apply_attr['apply_breakfast_num'] = isset($product_data['apply_breakfast_num']) ? (int) $product_data['apply_breakfast_num'] : 0;

        $result['apply_num'] = $apply_num;
        $result['discount_data'] = $discount_data;
        $result['apply_attr'] = $apply_attr;

        return $result;
    }

    /**
     * 获取产品列表
     * @param $order_data
     * @param $product_data
     * @param $attr
     * @return mixed
     */
    public static function getProductsList($order_data, $product_data, $attr)
    {
        $product_id = intval($order_data['product_id']);
        $product_data = self::processProductData($order_data, $attr);
        $product_data['product_id'] = $product_id;

        $list[0] = $product_data;
        return $list;
    }

    /**
     * 生成订单数据
     * @param $order_data
     * @param $computed_data
     * @return mixed
     */
    public static function composeOrderData($order_data, ComputedData $computed_data)
    {
        return $order_data;
    }

    /**
     * 生成订单产品数据
     * @param $product_data
     * @param $computed_data
     * @return null
     */
    public static function composeProductsData($product_data, ComputedData $computed_data)
    {
        $product_info = end($computed_data->products_info);

        $product_data['room_id'] = $product_info['room_info']['room_id'];
        $product_data['room_name'] = $product_info['room_info']['room_name'];
        $product_data['room_price'] = json_encode($product_info['room_rate']);
        $product_data['apply_price'] = $computed_data->apply_price;

        return $product_data;
    }
}