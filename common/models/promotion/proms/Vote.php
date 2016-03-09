<?php

namespace common\models\promotion\proms;

use common\models\promotion\PromConst;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivityVote as VoteAR;
use common\models\ar\PromotionActivityVoteOptions as OptionsAR;
use common\models\ar\PromotionActivityVotePrize as PrizeAR;

/**
 * 投票活动
 * Class Vote
 * @package common\models\promotion\proms
 */
Class Vote extends Promotion
{
    /**
     * 子活动AR
     * @var null|static
     */
    protected $_act_ar;

    /**
     * 投票选项AR
     * @var
     */
    protected $_option_ars;

    /**
     * 奖项AR
     */
    protected $_prize_ars;

    /**
     * 构造函数
     * Ordinary constructor.
     * @param $prom_ar
     * @param $act_ar
     * @param $option_ars
     * @param $prize_ars
     */
    protected function __construct($prom_ar, $act_ar, $option_ars, $prize_ars)
    {
        parent::__construct($prom_ar);
        $this->_act_ar = $act_ar;

        foreach($option_ars as $ar){
            $this->_option_ars[$ar->option_id] = $ar;
        }
        foreach($prize_ars as $ar){
            $this->_prize_ars[$ar->prize_id] = $ar;
        }
    }

    /**
     * 创建普通活动对象
     * @param $prom_id
     * @param $prom_ar
     * @param $act_ar
     * @return Ordinary|null
     */
    public static function getInstance($prom_id, $prom_ar=null, $act_ar=null)
    {
        if(empty($prom_ar)){
            $prom_ar = PromotionAR::findOne($prom_id);
        }
        if(empty($prom_ar)) return null;
        if(empty($act_ar)){
            $act_ar = VoteAR::findOne(['prom_id'=>$prom_ar->prom_id]);
        }
        if(empty($prom_ar)) return null;
        $option_ars = OptionsAR::findAll(['activity_id'=>$act_ar->activity_id]);
        $prize_ars = PrizeAR::findAll(['activity_id'=>$act_ar->activity_id]);

        return new Vote($prom_id, $prom_ar, $act_ar, $option_ars, $prize_ars);
    }

    /**
     * 获取具体类型活动的类别
     * @return int
     */
    public static function getType()
    {
        return PromConst::PROMOTION_TYPE_VOTE;
    }

    /**
     * 添加具体类型的活动
     * @param $prom_id
     * @param $activity_data
     * @param bool $get_id
     * @param $attr
     * @return VoteAR|int|null
     */
    public static function addActivity($prom_id, $activity_data, $get_id=false, $attr=null)
    {
        $vote_activity_ar = new VoteAR();

        $vote_activity_ar->setAttributes($activity_data['activity']);
        $vote_activity_ar->prom_id = $prom_id;

        if($vote_activity_ar->validate() && $vote_activity_ar->insert()){
            if(!self::_addOptions($vote_activity_ar->activity_id, $activity_data['options'])) return null;
            if(!self::_addPrizes($vote_activity_ar->activity_id, $activity_data['prizes'])) return null;
            return $get_id ? $vote_activity_ar->activity_id : $vote_activity_ar;
        }else{
            return null;
        }
    }

    /**
     * 获取具体类型的活动信息
     * @param bool $get_array
     * @param $attr
     * @return array|null|static
     */
    public function getActivity($get_array=false, $attr=null)
    {
        $act_data['activity'] =  $get_array ? $this->_act_ar->toArray() : $this->_act_ar;
        if($get_array){
            foreach($this->_option_ars as $key => $option_ar){
                $act_data['options'][$key] = $option_ar->toArray();
            }
            foreach($this->_prize_ars as $key => $prize_ar){
                $act_data['prizes'][$key] = $prize_ar->toArray();
            }
        }else{
            $act_data['options'] = $this->_option_ars;
            $act_data['prizes'] = $this->_prize_ars;
        }

        return $act_data;
    }

    /**
     * 更新活动具体信息
     * @param $activity_data
     * @param $attr
     * @return bool
     */
    public function updateActivity($activity_data, $attr=null)
    {
        //清除不允许修改字段，防止被用户数据覆盖
        unset($activity_data['activity']['prom_id']);
        unset($activity_data['activity']['activity_id']);
        unset($activity_data['activity']['activity_type']);
        $this->_act_ar->setAttributes($activity_data['activity']);

        if( $this->_act_ar->validate() && $this->_act_ar->save()){
            if($this->_updateOptions($activity_data['options']) === false) return false;
            if($this->_updatePrizes($activity_data['prizes']) === false) return false;
            return true;
        }else{
            return false;
        }
    }

    /**
     * 添加选项
     * @param $activity_id
     * @param $options
     * @return int
     */
    private static function _addOptions($activity_id, $options)
    {
        $option_count=0;
        foreach($options as $option){
            //添加选项
            $option_ar = new OptionsAR();
            $option_ar->activity_id = $activity_id;
            //$option_ar->option_type = $option['option_type'];
            //$option_ar->title = $option['title'];
            $option_ar->name = $option['name'];
            //$option_ar->description = $option['description'];
            $option_ar->list_order = $option['list_order'];
            if(!($option_ar->validate() && $option_ar->save())){
                return false;
            }
            $option_count++;
        }

        return $option_count;
    }

    /**
     * 添加奖品
     * @param $activity_id
     * @param $prizes
     * @return int|false
     */
    private static function _addPrizes($activity_id, $prizes)
    {
        $prize_count=0;
        foreach($prizes as $prize){
            //添加选项
            $prize_ar = new PrizeAR();
            $prize_ar->activity_id = $activity_id;
            $prize_ar->title = $prize['title'];
            $prize_ar->name = $prize['name'];
            if(!($prize_ar->validate() && $prize_ar->save())){
                return false;
            }
            $prize_count++;
        }

        return $prize_count;
    }
    /**
     * 添加选项
     * @param $options
     * @return int|false
     */
    private function _updateOptions($options)
    {
        $option_count=0;
        foreach($options as $key=>$option){
            $option_ar = OptionsAR::findOne(['option_id'=>$key, 'activity_id'=>$this->_act_ar->activity_id]);
            if(empty($option_ar)) continue;

            $option_ar->status = $option['status'];
            if(!$option_ar->validate()){
                return false;
            }
            if(!$option_ar->save()){
                return false;
            }
            $option_count++;
        }

        return $option_count;
    }

    /**
     * 添加奖品
     * @param $prizes
     * @return int|false
     */
    private function _updatePrizes($prizes)
    {
        $prize_count=0;
        foreach($prizes as $key=>$prize){
            $prize_ar = PrizeAR::findOne(['prize_id'=>$key, 'activity_id'=>$this->_act_ar->activity_id]);
            if(empty($prize_ar)) continue;

            $prize_ar->status = $prize['status'];
            if(!$prize_ar->validate()){
                return false;
            }
            if(!$prize_ar->save()){
                return false;
            }
            $prize_count++;
        }

        return $prize_count;
    }
}