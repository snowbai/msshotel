<?php

namespace common\models\order\orders\order_no;

/**
 * 订单编号格式 [8]日期 + [6+]酒店ID + [5]数字随机数
 * 注：
 * 若一天内单家酒店产生的订单超过5万条，需增加随机数位数
 * Class Simple
 * @package common\models\order\orders\order_no
 * @author Lyl <inspii@me.com>
 * @version 2.0
 */
Class Simple implements IOrderNo
{
    /**
     * 生成订单号
     * @param $data
     * @return null|string
     */
    public static function generate($data)
    {
        if(is_numeric($data)){
            $hotel_id = (int)$data;
        }elseif(isset($data['hotel_id'])){
            $hotel_id = (int)$data['hotel_id'];
        }else{
            return null;
        }

        $order_no = date('Ymd')
            . str_pad($hotel_id,6,'0', STR_PAD_LEFT) //长度为6位或以上
            . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        return $order_no;
    }

    /**
     * 获取算法信息
     * @param string $field
     * @return int|string
     */
    public static function getInfo($field='')
    {
        $info['no_type'] = 1;
        $info['version'] = '1.0';
        $info['about'] = '';

        switch($field){
            case 'no_type':
                return $info['no_type'];
            case 'version':
                return $info['version'];
            case 'about':
                return $info['about'];
            default:
                return $info;
        }
    }
}