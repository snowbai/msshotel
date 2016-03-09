<?php

namespace common\models\order\products\parser;

/**
 * 订单提交数据处理类
 * Interface IParser
 * @package common\models\order\products\parser
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
interface IParser
{
    /**
     * 解析产品数据
     * @param $product_data
     * @param $attr
     * @return mixed
     */
    public static function processProductData($product_data, $attr);

    /**
     * 获取购买产品列表（用于循环增减库存及计算价格）
     * @param $order_data
     * @param $product_data
     * @param $attr
     * @return mixed
     */
    public static function getProductsList($order_data, $product_data, $attr);

    /**
     * 合成订单数据（用于存入订单表）
     * @param $order_data
     * @param $computed_data
     * @return mixed
     */
    public static function composeOrderData($order_data, ComputedData $computed_data);

    /**
     * 合成订单产品数据（用于存入订单产品表）
     * @param $products_data
     * @param $computed_data
     * @return mixed
     */
    public static function composeProductsData($products_data, ComputedData $computed_data);

}