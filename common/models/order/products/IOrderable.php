<?php

namespace common\models\order\products;

/**
 * 可购买产品接口
 * Class IOrderable
 * @package common\models\order\products
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
interface IOrderable
{
    /**
     * 获取产品对象
     * @param $product_id
     * @param $attr
     * @return mixed
     */
    public static function getInstance($product_id, $attr);

    /**
     * 获取信息
     * @param $attr
     * @return array
     */
    public static function getTypeInfo($attr=null);

    /**
     * 获取产品信息
     * @return mixed
     */
    public function getInfo();


    /**
     * 获取产品预留数量
     * @return mixed
     */
    public function getReserveNum();

    /**
     * 获取产品剩余数量
     * @return mixed
     */
    public function getRemainNum();

    /**
     * 计算实际折后价格
     * @param $apply_num
     * @param $discount_data
     * @return mixed
     */
    public function calcApplyPrice($apply_num, $discount_data);

    /**
     * 调整预留数量
     * @param $new_num
     * @return mixed
     */
    public function adjustReserveNum($new_num);

    /**
     * 减少剩余数量
     * @param $force
     * @param $num
     * @return mixed
     */
    public function decRemainNum($num, $force=false);

    /**
     * 增加剩余数量
     * @param $force
     * @param $num
     * @return mixed
     */
    public function incRemainNum($num, $force=true);

}