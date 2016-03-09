<?php

namespace common\models\promotion\proms_join;

use common\models\promotion\PromConst;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivityScratch as ScratchAR;
use common\models\promotion\common\draw\rand_draw\RandDraw;
use common\models\promotion\common\draw\rand_draw\ByProbability;
use common\models\promotion\common\sn\SnObj;
use common\models\promotion\common\sn\SnLister;
use common\models\promotion\common\prize\PrizeLister;
use common\models\promotion\proms_join\common\DoCheck;
use common\models\promotion\proms_join\common\DoLog;
use common\models\promotion\proms_join\common\DoCalcRemains;

/**
 * 促销活动参与类
 * Class ScratchJoin
 * @package common\models\promotion\proms_join
 */
class ScratchJoin implements IPromotionJoin
{
    /**
     * 获取公用方法
     */
    use DoCalcRemains, DoLog, DoCheck;

    /**
     * 活动ID
     * @var PromotionAR
     */
    protected $_prom_ar;

    /**
     * 具体活动AR
     * @var ScratchAR
     */
    protected $_act_ar;

    /**
     * 构造函数
     * ScratchJoin constructor.
     * @param $prom_ar
     * @param $act_ar
     */
    protected function __construct($prom_ar, $act_ar)
    {
        $this->_prom_ar = $prom_ar;
        $this->_act_ar = $act_ar;
    }

    /**
     * 获取对象
     * @param $prom_id
     * @return ScratchJoin|null
     */
    public static function getInstance($prom_id)
    {
        $prom_ar = PromotionAR::findOne((int)$prom_id);
        if(empty($prom_ar)) return null;
        $act_ar = ScratchAR::findOne($prom_id);
        if(empty($act_ar)) return null;

        return new ScratchJoin($prom_ar, $act_ar);
    }

    public function getInfo()
    {
        $info['promotion'] = $this->_prom_ar->toArray();
        $info['activity'] = $this->_act_ar->toArray();
        $prize_arr = PrizeLister::getListEx($this->_act_ar->prom_id, $this->_act_ar->activity_id, array(), 0, null, false, true);
        foreach($prize_arr as $prize){
            $prize_id = $prize['prize_id'];
            $info['prizes'][$prize_id] = $prize;
        }
        return $info;
    }

    public function draw($member_id, $member_type='member_id')
    {
        //判断活动是否合法
        if(!$this->_checkPromValid($this->_prom_ar)) {
            return ['prize_id'=>PromConst::PRIZE_PROM_INVALID, 'prize_name'=>'活动不合法'];
        }

        //判断会员抽奖次数是否达到限制
        $is_limit_reached = $this->_isMemLmtReached($this->_prom_ar, $this->_act_ar->activity_id,$member_id,$member_type);
        if($is_limit_reached == PromConst::ERR_PRIZE_DRAW_LIMIT_REACHED){
            //如果抽奖次数达到限制，则返回负数错误码
            return ['prize_id'=>-abs($is_limit_reached),'prize_name'=>'今天机会用完啦'];
        }

        if($is_limit_reached == PromConst::ERR_PRIZE_WIN_LIMIT_REACHED){
            $prize_id = -abs($is_limit_reached);
            $prize_name = '谢谢参与';
        }else{
            //进行抽奖
            $today_awards_remains = $this->_getTodayRemains($this->_prom_ar, $this->_act_ar->activity_id, $this->_prom_ar->start_time, $this->_prom_ar->end_time);
            if($today_awards_remains > 0){
                //抽奖
                $data = $this->_getAwardsData();
                $drawer = new RandDraw(new ByProbability());
                $prize_id = $drawer->draw($data);
                $prize_name = $data['prize_name'];
            }else{
                //如果今天奖品已发完，则不能再中奖
                $prize_id = PromConst::PRIZE_TODAY_OUT;
                $prize_name = '谢谢参与';
            }
        }

        $prize_info['prize_id'] = $prize_id;
        $prize_info['prize_name'] = $prize_name;

        return $prize_info;
    }

    /**
     * 分配奖品
     * @param $prize_id
     * @param $member_id
     * @param string $member_type
     * @return array
     */
    public function givePrizeAndLog($prize_id, $member_id, $member_type='member_id')
    {
        //判断活动是否合法
        if(!$this->_checkPromValid($this->_prom_ar)) {
            return ['prize_id'=>PromConst::PRIZE_PROM_INVALID, 'sn_detail'=>null];
        }

        //判断会员抽奖次数是否达到限制
        $is_limit_reached = $this->_isMemLmtReached($this->_prom_ar, $this->_act_ar->activity_id,$member_id,$member_type);
        if($is_limit_reached == PromConst::ERR_PRIZE_DRAW_LIMIT_REACHED){
            //如果抽奖次数达到限制，则返回负数错误码
            return ['prize_id'=>-abs($is_limit_reached),'sn_detail'=>null];
        }

        if($prize_id > 0){
            //分配SN
            $sn_token = $member_id;
            $sn_obj = SnObj::getAnVacant($this->_prom_ar->prom_id, $this->_act_ar->activity_id, $prize_id, $sn_token);

            if(!empty($sn_obj)){
                if($sn_obj->give([$member_type=>$member_id],$sn_token)){
                    $sn_info = $sn_obj->getInfo();
                }else{
                    $sn_obj->setVacant($sn_token); //分配失败则退还SN码
                    $prize_id = PromConst::PRIZE_GET_FAILED;
                    $sn_info = null;
                }
            }else{
                //无法获取到空余的SN码，则不能中奖
                $prize_id = PromConst::PRIZE_GET_FAILED;
                $sn_info = null;
            }
        }else{
            $sn_info = null;
        }

        $this->_log($this->_prom_ar->prom_id, $this->_act_ar->activity_id, $prize_id, 0, $member_id, $member_type);
        $prize_info['prize_id'] = $prize_id;
        $prize_info['sn_detail'] = $sn_info;

        return $prize_info;
    }

    /**
     * 获取会员剩余的抽奖次数
     * @param $member_id
     * @param string $member_type
     * @return int
     */
    public function getRemainDrawTimes($member_id, $member_type='member_id')
    {
        //判断活动是否合法
        if(!$this->_checkPromValid($this->_prom_ar)) return 0;

        $remain_info = $this->_getMemRemainTimes($this->_prom_ar, $this->_act_ar->activity_id, $member_id, $member_type);
        if(empty($remain_info)){
            return 9999;
        }else{
            return $remain_info['draw_remain'];
        }
    }

    /**
     * 领取奖品
     * @param $sn_no
     * @param $winner_data
     * @return bool
     */
    public static function getPrize($sn_no, $winner_data)
    {
        $sn_obj = SnObj::getInstance($sn_no);
        return $sn_obj->get($winner_data);
    }

    /**
     * 生成奖品数组，用于抽奖
     * @return array
     */
    protected function _getAwardsData()
    {
        if(empty($this->_prize_ars)) return array();

        $total_prize_point = 0;
        foreach($this->_prize_ars as $prize){
            $prize_id = $prize->prize_id;

            $awards[$prize_id]['id'] = $prize_id;
            $awards[$prize_id]['prize_type'] = $prize->prize_type;
            $awards[$prize_id]['title'] = $prize->title;
            $awards[$prize_id]['name'] = $prize->name;
            $awards[$prize_id]['point'] = $prize->point;

            $total_prize_point += $prize['point'];
        }
        $try_again_point = $no_prize_point = abs(100 - $total_prize_point) / 2;

        //获取各奖项剩余数量
        $statistics = SnLister::getStatisticsEx($this->_prom_ar->prom_id, $this->_act_ar->activity_id);
        $prize_statistic = $statistics['prize_statistic'];
        foreach($prize_statistic as $prize_id=>$status_arr){
            if(isset($awards[$prize_id]['id'])){
                $awards[$prize_id]['remain_num'] = isset($status_arr[0]) ? $status_arr[0] : 0;
            }
        }

        $awards[PromConst::PRIZE_NONE] = [
            'id'=>PromConst::PRIZE_NONE,
            'prize_type'=>PromConst::PRIZE_NONE,
            'title'=> '未中奖',
            'name'=>'很遗憾，没抽中！',
            'remain_num'=>65535,
            'point'=>$no_prize_point
        ];
        $awards[PromConst::PRIZE_TRY_AGAIN] = [
            'id'=>PromConst::PRIZE_TRY_AGAIN,
            'prize_type'=>PromConst::PRIZE_TRY_AGAIN,
            'title'=> '再来一次',
            'name'=>'再来一次！',
            'remain_num'=>65535,
            'point'=>$try_again_point
        ];

        return $awards;
    }

}