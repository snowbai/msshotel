<?php

namespace common\models\promotion\common\limits;

use common\models\promotion\PromConst;
use common\models\ar\PromotionLimit;

/**
 * 促销活动会员抽奖及中奖高级限制类
 * 用与后台设置抽奖限制，及前台判断会员是否达到限制
 * Class MemLimiter
 * @package common\models\promotion\limits
 */
class MemLimiter
{
    /**
     * 活动ID
     * @var
     */
    protected $_prom_id;

    /**
     * 活动子ID
     * @var
     */
    protected $_activity_id;

    /**
     * 高级限制器AR对象
     * @var null|static
     */
    protected $_lmt_info_ar;

    /**
     * 构造函数
     * 若activity_id为0，表示限制为总限制，不为0则仅对单个子活动生效
     * @param $prom_id
     * @param $activity_id
     * @param $_lmt_info_ar
     */
    public function __construct($prom_id, $activity_id, $_lmt_info_ar=null)
    {
        if(!empty($_lmt_info_ar)){
            $this->_prom_id = $_lmt_info_ar->prom_id;
            $this->_activity_id = $_lmt_info_ar->activity_id;
            $this->_lmt_info_ar = $_lmt_info_ar;
        }elseif($prom_id>0){
            $this->_prom_id = (int) $prom_id;
            $this->_activity_id = (int) $activity_id;
            $this->_lmt_info_ar = PromotionLimit::find()->where(['prom_id'=>$prom_id, 'activity_id'=>$activity_id])->one();
        }
    }

    /**
     * 添加或修改活动限制（后台使用）
     * 注意：需确保具体活动启用高级限制，否则限制不生效
     * @param $lmt_data
     * @return int
     */
    public function setLimit($lmt_data)
    {
        //如果活动ID不合法，返回错误
        if($this->_prom_id <= 0){
            return 0;
        }
        //如果数据表中，没有当前活动的限制信息，则添加一条，否则只在原有记录上进行修改
        if(empty($this->_lmt_info_ar)){
            $this->_lmt_info_ar = new PromotionLimit();
            $this->_lmt_info_ar->prom_id = $this->_prom_id;
            $this->_lmt_info_ar->activity_id = $this->_activity_id;
        }

        unset($lmt_data['lmt_id']);
        unset($lmt_data['prom_id']);
        unset($lmt_data['activity_id']);
        $this->_lmt_info_ar->setAttributes($lmt_data);

        if($this->_lmt_info_ar->save()){
            return $this->_lmt_info_ar->lmt_id;
        }else{
            return 0;
        }
    }

    /**
     * 获取活动限制信息
     * @return mixed
     */
    public function getLimit()
    {
        if(empty($this->_lmt_info_ar)) return null;
        return $this->_lmt_info_ar->toArray();
    }

    /**
     * 计算剩余的抽奖中奖次数
     * @param $used_count, 会员已经抽奖及中奖数量，格式为['day_count'=>value, 'day_win_count'=>value, ...]
     * @return null|array, null无限制
     */
    public function calcRemainTimes($used_count)
    {
        if(empty($this->_lmt_info_ar)){
            return null; //无限制
        }

        $mem_remain_info = $this->_getMemRemainTimes($used_count);
        $prom_remain_info = $this->_getPromRemainTimes($used_count);

        if(!empty($mem_remain_info)&&!empty($prom_remain_info)){
            $draw_remain = min($mem_remain_info['draw_remain'], $prom_remain_info['draw_remain']);
            $win_remain = min($mem_remain_info['win_remain'], $prom_remain_info['win_remain']);
            $remain_info = ['draw_remain'=>$draw_remain, 'win_remain'=>$win_remain];
        }elseif(!empty($mem_remain_info)){
            $remain_info = $mem_remain_info;
        }elseif(!empty($prom_remain_info)){
            $remain_info = $prom_remain_info;
        }else{
            $remain_info = null;
        }

        return $remain_info;
    }

    /**
     * 计算抽奖是否达到限制
     * @param $used_count, 会员已经抽奖及中奖数量，格式为['day_count'=>value, 'day_win_count'=>value, ...]
     * @return int, 0没有达到限制，1抽奖次数达到限制，2中奖次数达到限制
     */
    public function calcIsReached($used_count)
    {
        $remain_times = $this->calcRemainTimes($used_count);
        if(empty($remain_times)){
            return 0;
        }

        $draw_remain = $remain_times['draw_remain'];
        $win_remain = $remain_times['win_remain'];

        if($draw_remain<=0){
            return PromConst::ERR_PRIZE_DRAW_LIMIT_REACHED;
        }elseif($win_remain<=0){
            return PromConst::ERR_PRIZE_WIN_LIMIT_REACHED;
        }else{
            return 0;
        }
    }

    /**
     * 计算会员剩余机会次数
     * @param $used_count
     * @return array|null
     */
    protected function _getMemRemainTimes($used_count)
    {
        if(empty($this->_lmt_info_ar)) return null;

        switch($this->_lmt_info_ar->lmt_level) {
            //对会员抽奖及中奖次数进行限制
            case PromConst::LIMIT_NONE: //未开启限制
                return null; //无限制
            case PromConst::LIMIT_MEM_BY_DAY: //仅限制会员每天抽奖及中奖次数
            case PromConst::LIMIT_ALL_BY_DAY:
                $draw_remain = max($this->_lmt_info_ar->mem_day_lmt - $used_count['mem_today_count'], 0);
                $win_remain = max($this->_lmt_info_ar->mem_day_win_lmt - $used_count['mem_today_win_count'], 0);
                break;
            case PromConst::LIMIT_MEM_BY_DURATION: //仅限制会员活动期间抽奖及中奖次数
            case PromConst::LIMIT_ALL_BY_DURATION:
                $draw_remain = max($this->_lmt_info_ar->mem_total_lmt - $used_count['mem_total_count'], 0);
                $win_remain = max($this->_lmt_info_ar->mem_total_win_lmt - $used_count['mem_total_win_count'], 0);
                break;
            case PromConst::LIMIT_MEM_BY_DAY_AND_DURATION: //同时限制会员活动期间及每天抽奖及中奖次数
            case PromConst::LIMIT_ALL_BY_DAY_AND_DURATION:
                $day_draw_remain = max($this->_lmt_info_ar->mem_day_lmt - $used_count['mem_today_count'], 0);
                $day_win_remain = max($this->_lmt_info_ar->mem_day_win_lmt - $used_count['mem_today_win_count'], 0);

                $total_draw_remain = max($this->_lmt_info_ar->mem_total_lmt - $used_count['mem_total_count'], 0);
                $total_win_remain = max($this->_lmt_info_ar->mem_total_win_lmt - $used_count['mem_total_win_count'], 0);

                $draw_remain = min($day_draw_remain, $total_draw_remain);
                $win_remain = min($day_win_remain, $total_win_remain);
                break;
            default:
                return null;
        }
        return ['draw_remain'=>$draw_remain, 'win_remain'=>$win_remain];
    }

    /**
     * 计算活动总的剩余次数
     * @param $used_count
     * @return array|null
     */
    protected function _getPromRemainTimes($used_count)
    {
        if(empty($this->_lmt_info_ar)) return null;

        switch($this->_lmt_info_ar->lmt_level) {
            case PromConst::LIMIT_NONE: //未开启限制
                return null; //无限制
            case PromConst::LIMIT_BY_HOUR: //限制每天抽奖及中奖次数
            case PromConst::LIMIT_ALL_BY_HOUR:
                $draw_remain = max($this->_lmt_info_ar->day_lmt - $used_count['today_count'],0);
                $win_remain = max($this->_lmt_info_ar->day_win_lmt - $used_count['today_win_count'],0);
                break;
            case PromConst::LIMIT_BY_DURATION: //限制活动期间抽奖及中奖次数
            case PromConst::LIMIT_ALL_BY_DURATION:
                $draw_remain = max($this->_lmt_info_ar->total_lmt - $used_count['total_count'],0);
                $win_remain = max($this->_lmt_info_ar->total_win_lmt - $used_count['total_win_count'],0);
                break;
            case PromConst::LIMIT_BY_DAY_AND_DURATION: //同时限制活动期间及每天抽奖及中奖次数
            case PromConst::LIMIT_ALL_BY_DAY_AND_DURATION:
                $day_draw_remain = max($this->_lmt_info_ar->day_lmt - $used_count['today_count'],0);
                $day_win_remain = max($this->_lmt_info_ar->day_win_lmt - $used_count['today_win_count'],0);

                $total_draw_remain = max($this->_lmt_info_ar->total_lmt - $used_count['total_count'],0);
                $total_win_remain = max($this->_lmt_info_ar->total_win_lmt - $used_count['total_win_count'],0);

                $draw_remain = min($day_draw_remain, $total_draw_remain);
                $win_remain = min($day_win_remain, $total_win_remain);
                break;
            default:
                return null;
        }
        return ['draw_remain'=>$draw_remain, 'win_remain'=>$win_remain];
    }
}