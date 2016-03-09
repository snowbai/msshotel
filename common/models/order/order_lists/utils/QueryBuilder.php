<?php

namespace common\models\order\order_lists\utils;

use common\models\order\OrderConst;

/**
 * 查询构建器
 * Class QueryBuilder
 * @package common\models\order\order_lists\utils
 */
class QueryBuilder
{
    /**
     * 生成订单状态查询条件
     * @param $status_names
     * @return mixed
     */
    public static function parseStatusName($status_names)
    {
        $result['status_set_arr'] = array();
        $result['status_unset_arr'] = array();
        if(is_string($status_names)){
            return self::_getStatusArray($status_names);
        }elseif(is_array($status_names)){
            foreach($status_names as $name){
                $arr = self::_getStatusArray($name);
                $result['status_set_arr'] = array_merge($result['status_set_arr'], $arr['status_set_arr']);
                $result['status_unset_arr'] = array_merge($result['status_unset_arr'], $arr['status_unset_arr']);
            }
        }else{
            $result['status_set_arr'] = array();
            $result['status_unset_arr'] = array();
        }
        return $result;
    }

    /**
     * 生成订单状态查询条件
     * @param $set_arr
     * @param $unset_arr
     * @param $status_filed_name
     * @return int|string
     */
    public static function getStatusQueryCondition($set_arr, $unset_arr, $status_filed_name)
    {
        $status_filed_name = strval($status_filed_name);

        $bits_set = 0;
        foreach( $set_arr as $bit){
            $bits_set |= (int)$bit;
        }
        if($bits_set == 0){
            $set_condition = '';
        }else{
            $set_condition = $status_filed_name.' & '.$bits_set.' = '.$bits_set;
        }

        $bits_unset = 0;
        foreach( $unset_arr as $bit){
            $bits_unset |= (int)$bit;
        }
        if($bits_unset == 0){
            $unset_condition = '';
        }else{
            $unset_condition = $status_filed_name. ' & '. $bits_unset. ' = 0';
        }

        $query_condition = '';
        if(!empty($set_condition)){
            $query_condition = $set_condition;
        }
        if(!empty($unset_condition)){
            if(empty($query_condition)){
                $query_condition = $unset_condition;
            }else{
                $query_condition = '( '.$set_condition.' ) AND ( '.$unset_condition.' )';
            }
        }
        if(empty($query_condition)){
            $query_condition = 1;
        }

        return $query_condition;
    }

    /**
     * 根据状态名称获取订单状态查询数组
     * @param $status_name
     * @return mixed
     */
    protected function _getStatusArray($status_name)
    {
        $set_arr = array();
        $unset_arr = array();
        switch($status_name){
            case '未确认':
                $unset_arr[] = OrderConst::STATUS_BIT_CONFIRMED;
                break;
            case '已确认':
                $set_arr[] = OrderConst::STATUS_BIT_CONFIRMED;
                break;

            case '未支付':
                $unset_arr[] = OrderConst::STATUS_BIT_PAY_PAID;
                break;
            case '已支付':
                $set_arr[] = OrderConst::STATUS_BIT_PAY_PAID;
                break;
            case '退款订单':
                $set_arr[] = OrderConst::STATUS_BIT_PAY_REFUND_REQUESTED;
                break;

            case '未到店':
                $unset_arr[] = OrderConst::STATUS_BIT_DEALT_SUCCESS;
                break;
            case '已到店':
                $set_arr[] = OrderConst::STATUS_BIT_DEALT_SUCCESS;
                break;

            case '续住订单':
                //$unset_arr[] = OrderConst::STATUS_BIT_;
                break;
        }

        $result['status_set_arr'] = $set_arr;
        $result['status_unset_arr'] = $unset_arr;
        return $result;
    }
}
