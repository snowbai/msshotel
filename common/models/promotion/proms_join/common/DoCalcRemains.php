<?php

namespace common\models\promotion\proms_join\common;

use common\models\promotion\PromConst;
use common\models\promotion\common\log\DrawLogger;
use common\models\promotion\common\limits\MemLimiter;
use common\models\promotion\common\sn\SnLister;

/**
 * 判断抽奖中奖剩余次数
 * Class DoCalcRemains
 * @package common\models\promotion\proms_join\common
 */
trait DoCalcRemains
{
    /**
     * 判断会员抽奖或中奖次数是否已经达到限制
     * @param $prom_ar
     * @param $act_id
     * @param $member_id
     * @param $member_type
     * @return int
     */
    protected static function _isMemLmtReached($prom_ar, $act_id, $member_id,$member_type)
    {
        $remain_times = static::_getMemRemainTimes($prom_ar, $act_id, $member_id, $member_type);
        if(empty($remain_times)){
            return 0;//未达到限制
        }

        $draw_remain = $remain_times['draw_remain'];
        $win_remain = $remain_times['win_remain'];

        if($draw_remain<=0){
            return PromConst::ERR_PRIZE_DRAW_LIMIT_REACHED;//抽奖达到限制
        }elseif($win_remain<=0){
            return PromConst::ERR_PRIZE_WIN_LIMIT_REACHED;//中奖达到限制
        }else{
            return 0;//未达到限制
        }
    }

    /**
     * 默认的会员活动限制
     * 若限制数量为0，则表示未做限制
     * @param $prom_ar
     * @param $act_id
     * @param $member_id
     * @param $member_type
     * @return array|null
     */
    protected static function _getMemRemainTimes($prom_ar, $act_id, $member_id,$member_type)
    {
        if(empty($prom_ar)) return ['draw_remain'=>0, 'win_remain'=>0];

        if($prom_ar->use_advanced_lmt==0){
            //限制每天抽奖次数和活动期间中奖次数
            $mem_lmt_num = $prom_ar->mem_day_lmt_num;
            $mem_win_lmt_num = $prom_ar->mem_total_win_lmt_num;
            if($mem_lmt_num==0 && $mem_win_lmt_num==0){
                return null; //无限制
            }

            $logger = new DrawLogger($prom_ar->prom_id, $act_id);
            $counts_info = $logger->getMemCounts($member_id,$member_type);

            $remain_info['draw_remain'] = max( $mem_lmt_num - $counts_info['mem_today_count'] , 0);
            $remain_info['win_remain'] = max( $mem_win_lmt_num - $counts_info['mem_total_win_count'], 0);
        }else{
            $logger = new DrawLogger($prom_ar->prom_id, $act_id);
            $counts_info = $logger->getMemCounts($member_id,$member_type);
            $limiter = new MemLimiter($prom_ar->prom_id, $act_id);

            $remain_info = $limiter->calcRemainTimes($counts_info);
        }

        return $remain_info;
    }

    /**
     * 判断今天还能出奖的数量
     * @param $prom_id
     * @param $act_id
     * @param $prom_start_time
     * @param $prom_end_time
     * @return mixed
     */
    protected static function _getTodayRemains($prom_id, $act_id, $prom_start_time, $prom_end_time)
    {
        $statistics = SnLister::getStatisticsEx($prom_id, $act_id);
        $total_awards_num = $statistics['total'];
        $status_count_arr = $statistics['status'];
        $total_awards_remain = isset($status_count_arr[0]) ? $status_count_arr[0] : 0;

        $total_days = (strtotime(substr($prom_end_time,0,10)) - strtotime(substr($prom_start_time,0,10))) /  86400;
        $passed_days = (strtotime(date('Y-m-d')) - strtotime(substr($prom_start_time,0,10))) /  86400;
        $passed_days = min($passed_days,$total_days-1);

        $avg_awards_num = $total_awards_num / $total_days;
        $today_awards_num = floor($avg_awards_num * ($passed_days+1));
        $today_awards_remains = $today_awards_num - ($total_awards_num - $total_awards_remain);

        return max(0,$today_awards_remains);
    }
}