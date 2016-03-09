<?php

namespace common\models\order\orders\order_no;

/**
 * 订单编号码生成算法接口
 * 获得的订单编号唯一性需自行判断
 * Interface IAlgorithm
 * @package common\models\order\orders\order_no
 * @author Lyl <inspii@me.com>
 * @version 2.0
 */
interface IOrderNo
{
    /**
     * 生成一个订单编号
     * @param $data
     * @return mixed
     */
    public static function generate($data);

    /**
     * 获取算法信息
     * @param $field
     * @return mixed
     */
    public static function getInfo($field='');
}