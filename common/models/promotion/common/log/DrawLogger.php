<?php

namespace common\models\promotion\common\log;

use common\models\ar\PromotionDrawLog;
use common\models\promotion\PromConst;

/**
 * 促销活动抽奖日志类
 * Class DrawLogger
 * @package common\models\promotion\common\log
 */
class DrawLogger
{
    /**
     * 活动ID
     * @var
     */
    protected $_prom_id;

    /**
     * 子活动ID
     * @var int
     */
    protected $_activity_id;

    /**
     * 构造函数
     * @param $prom_id
     * @param $activity_id
     */
    public function __construct($prom_id,$activity_id)
    {
        $this->_prom_id = (int)$prom_id;
        $this->_activity_id = (int)$activity_id;
    }

    /**
     * 对用户抽奖进行记录
     * @param $data
     * @return int
     * @throws \Exception
     */
    public function log($data)
    {
        $draw_log_ar = new PromotionDrawLog();
        $draw_log_ar->setAttributes($data);
        $draw_log_ar->prom_id = $this->_prom_id;
        $draw_log_ar->activity_id = $this->_activity_id;

        if ($draw_log_ar->validate()) {
            if( !$draw_log_ar->insert() ){
                return $draw_log_ar->log_id;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    /**
     * 获取活动抽奖或中奖统计
     * @param string $type
     * @param $start_time
     * @param $end_time
     * @return int|string
     */
    public function getCounts($type='draw', $start_time=null, $end_time=null)
    {
        return self::getMemCountsEx($this->_prom_id, $this->_activity_id, $type, $start_time, $end_time);
    }

    /**
     * 获取用户抽奖统计
     * @param $member_id
     * @param string $member_type
     * @param $start_time
     * @param $end_time
     * @return mixed
     */
    public function getMemCounts($member_id, $member_type='member_id', $start_time=null, $end_time=null)
    {
        return self::getMemCountsEx($this->_prom_id, $this->_activity_id, $member_id, $member_type, $start_time, $end_time);
    }

    /**
     * 获取活动抽奖或中奖统计
     * @param $prom_id
     * @param $activity_id
     * @param string $type
     * @param null $start_time
     * @param null $end_time
     * @return int|string
     */
    public static function getCountsEx($prom_id, $activity_id, $type='draw', $start_time=null, $end_time=null)
    {
        $query = PromotionDrawLog::find()->where(['prom_id' => $prom_id]);
        if($activity_id!==null){
            $query->andWhere(['activity_id'=>(int)$activity_id]);
        }
        if(!empty($start_time)){
            $query->andWhere(['>','add_time',$start_time]);
        }
        if(!empty($end_time)){
            $query->andWhere(['<','add_time',$end_time]);
        }
        if($type=='win'){
            $query->andWhere(['>', 'prize_type', 0]);
        }

        $counts = $query->count();
        return $counts;
    }

    /**
     * 获取用户抽奖统计信息
     * @param $prom_id
     * @param $activity_id
     * @param $member_id
     * @param string $member_type
     * @param null $start_time
     * @param null $end_time
     * @return mixed
     */
    public static function getMemCountsEx($prom_id, $activity_id, $member_id, $member_type='member_id', $start_time=null, $end_time=null)
    {
        $query = PromotionDrawLog::find()->select('prize_type,add_time')->where(['prom_id'=>$prom_id]);
        if($activity_id!==null){
            $query->andWhere(['activity_id'=>$activity_id]);
        }

        switch($member_type){
            case 'member_id':
            case 'open_id':
            case 'phone':
                $query->andWhere([$member_type => $member_id]);
                break;
            default:
                $query->andWhere(['exch_type'=>(int)$member_type,'exch_value' => $member_id]);
        }
        if(!empty($start_time)){
            $query->andWhere(['>','add_time',$start_time]);
        }
        if(!empty($end_time)){
            $query->andWhere(['<','add_time',$end_time]);
        }

        $logs = $query->asArray()->all();
        $mem_used_amount = self::_prizeCounter($logs);//对返回的数组进行统计（若单个活动单个会员的抽奖数据较多，可能存在性能问题）

        $used_info['mem_total_count'] = $mem_used_amount['total_count'];
        $used_info['mem_total_win_count'] = $mem_used_amount['total_win_count'];
        $used_info['mem_today_count'] = $mem_used_amount['today_count'];
        $used_info['mem_today_win_count'] = $mem_used_amount['today_win_count'];
        return $used_info;
    }

    /**
     * 对抽奖日志进行统计
     * @param $array
     * @return array
     */
    private static function _prizeCounter($array)
    {
        $total_count = 0;
        $total_win_count = 0;
        $today_count = 0;
        $today_win_count = 0;
        $today_start = strtotime(date('Y-m-d'));
        $today_end = $today_start + 86400;

        foreach($array as $row){
            $add_time = strtotime($row['add_time']);
            $prize_type = $row['prize_type'];

            if($prize_type>0){
                if($today_start < $add_time && $today_end > $add_time){
                    $today_win_count++;
                    $today_count++;
                }
                $total_win_count++;
                $total_count++;
            }else{
                switch($prize_type){
                    case PromConst::PRIZE_TRY_AGAIN://再来一次
                    case PromConst::PRIZE_REACH_DRAW_LIMIT://抽奖机会已经用完
                    case PromConst::PRIZE_SEVER_ERROR://服务器错误
                        //不计入抽奖次数
                        break;
                    case PromConst::PRIZE_REACH_WIN_LIMIT://中奖机会已经用完
                    case PromConst::PRIZE_NONE://未中奖
                    default://其他
                        //按未中奖处理
                        if($today_start < $add_time && $today_end > $add_time){
                            $today_count++;
                        }
                        $total_count++;
                }
            }
        }

        $result = [
            'total_count' => $total_count,
            'total_win_count' => $total_win_count,
            'today_count' => $today_count,
            'today_win_count' => $today_win_count,
        ];

        return $result;
    }
}