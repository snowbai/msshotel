<?php

namespace common\models\promotion\proms_join\common;

use common\models\promotion\PromConst;
use common\models\promotion\common\log\DrawLogger;

/**
 * 对用户抽奖进行记录
 * Class DoLog
 * @package common\models\promotion\proms_join\common
 */
trait DoLog
{
    /**
     * 对抽奖进行log
     * @param $prom_id
     * @param $act_id
     * @param $prize_id
     * @param $prize_type
     * @param $member_id
     * @param $member_type
     * @return bool|int
     */
    protected function _log($prom_id, $act_id, $prize_id, $prize_type, $member_id,$member_type)
    {
        $not_log_arr = [
            PromConst::PRIZE_NOT_LOGIN,
            PromConst::PRIZE_REACH_DRAW_LIMIT,
            PromConst::PRIZE_PROM_INVALID,
        ];

        //对抽奖结果进行Log（不对没有抽奖机会的抽奖进行log）
        if( in_array($prize_id, $not_log_arr) || in_array($prize_type, $not_log_arr)) return false;

        $log_data = [
            'prize_id'=>$prize_id,
            'prize_type'=>$prize_type,
        ];
        if($member_type=='member_id' || $member_type=='open_id' || $member_type=='phone') {
            $log_data[$member_type] = $member_id;
        }else{
            $log_data['exch_type'] = intval($member_type);
            $log_data['exch_value'] = $member_id;
        }

        $logger = new DrawLogger($prom_id,$act_id);
        return $logger->log($log_data);
    }
}