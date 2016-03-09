<?php

namespace common\models\promotion\common\draw\rand_draw;

/**
 * 按时间点进行分配奖品
 * Class ByTime
 * @package common\models\promotion\draw\rand_draw_algorithms
 */
Class ByTime implements IAlgorithm
{
    /**
     * @param $data, 格式为['prizes'=>[['id'=>value, 'set_num'=>value, 'remain_num'=>value],...]]
     * @return int|string
     */
    public function draw($data)
    {
        if(!is_array($data)) return null;
        $optimal_start_hour = isset($data['optimal_start_hour']) ? $data['optimal_start_hour'] : '08:00:00';
        $optimal_end_hour = isset($data['optimal_end_hour']) ? $data['optimal_end_hour'] : '22:00:00';
        $prizes = isset($data['prizes']) ? $data['prizes'] : $data;

        $optimal_start_time = date('Y-m-d').' '.$optimal_start_hour;
        $optimal_end_time = date('Y-m-d').' '.$optimal_end_hour;

        $total_amounts = 0;
        $total_left = 0;
        $awards_arr = array();
        foreach ($prizes as $prize){
            $prize_id = $prize['id'];
            $set_num = $prize['set_num'];
            $remain_num = $prize['remain_num'];
            if($remain_num>=0 && $set_num>=0){
                $awards_arr[$prize_id] = $remain_num;
                $total_amounts += $set_num;
                $total_left += $remain_num;
            }
        }

        if($total_left>0 && time()>strtotime($optimal_end_time)){ //如果过了最佳抽奖时间点还有奖品，则直接中奖 ，保证奖品能发完
            $win = 1;
        }else{//如果还在设置的最佳抽奖时间点，则按时间点分配奖品，防止奖品一下子被抢完
            $win = self::_drawByTime($optimal_start_time,$optimal_end_time,$total_amounts,$total_left);
        }

        if($win) {
            $prize_id = Rander::randKey($awards_arr);
        }else{
            $prize_id = 0;
        }

        return $prize_id;
    }

    /**
     * 按时间点发奖
     * 将奖项平均分配到每个时间点，让用户可以抽取在某一具体时间前的所有时间点的奖品（如果没有被抽完）
     * @param $start_time
     * @param $end_time
     * @param $amount, 奖品总量
     * @param $left, 奖品实时剩余量
     * @param array $time_offset_array, 需保证随机数组对活动一直不会变化, 比如从常量或数据库读取
     * @return bool
     */
    protected static function _drawByTime($start_time,$end_time, $amount, $left, $time_offset_array=array())
    {
        $left_time = strtotime($end_time) - time();
        $delta = (strtotime($end_time)-strtotime($start_time)) / $amount;

        //如果已经没有奖品，活动已经结束，或者奖品平均分配时间小于0，则返回0
        if($left_time <= 0
            || $amount <= 0 || $left <=0
            || $delta <= 0){
            return false;
        }

        //按随机数组生成随机偏移值
        $count = count($time_offset_array);
        if($count>0){
            $time_offset = $time_offset_array[ ($left_time/$delta)%$count ]; //按时间段从随机数组获取偏移值
            $time_offset = abs($time_offset%$delta); //偏移值必须为正数，且不能大于delta
        }else{
            $time_offset = 0;
        }

        //计算当前时间点是否还有奖品可以发出
        if(($left_time+$time_offset)/$delta <= $left){
            return true;
        }else{
            return false;
        }
    }
}