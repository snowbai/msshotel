<?php

namespace common\models\order\products;

use common\models\base\Errorable;

/**
 * 可购买产品基类
 * Class OrderableBase
 * @package common\models\order\products
 */
abstract class OrderableBase extends Errorable implements IOrderable
{
    /**
     * 产品ID
     * @var
     */
    protected $_product_id;

    /**
     * 产品属性
     * @var null
     */
    protected $_attr;

    /**
     * 构造函数
     * OrderableProduct constructor.
     * @param $product_id
     * @param null $attr
     */
    protected function __construct($product_id, $attr=null)
    {
        $this->_product_id = $product_id;
        $this->_attr = $attr;
    }

    /**
     * 计算折后价
     * @param $price
     * @param $discount_data
     * @param null $attr
     * @return mixed
     */
    public static function _calculateDiscount($price, $discount_data, $attr=null)
    {
        if(is_array($discount_data)){
            $discount_value = isset($discount_data['member_discount_value']) ? $discount_data['member_discount_value'] : 0;
            $apply_integral_money = isset($discount_data['apply_integral_money']) ? $discount_data['apply_integral_money'] : 0;
        }else{
            $discount_value = $discount_data;
            $apply_integral_money = 0;
        }

        $discount_value = $discount_value>0 && $discount_value<100 ? $discount_value : 100;
        $apply_price = $price * $discount_value / 100 - $apply_integral_money;
        return max(0, $apply_price);
    }
}