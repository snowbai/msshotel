<?php

namespace common\models\promotion\proms;

use common\models\promotion\PromConst;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivityScratch as ScratchAR;
use common\models\promotion\common\prize\PrizeObj;
use common\models\promotion\common\prize\PrizeLister;
use common\models\promotion\common\sn\SnObj;

/**
 * 刮刮乐活动
 * Class Scratch
 * @package common\models\promotion\proms
 */
Class Scratch extends Promotion
{
    /**
     * 子活动AR
     * @var ScratchAR
     */
    protected $_act_ar;

    /**
     * 奖项AR
     */
    protected $_prize_ars;

    /**
     * 构造函数
     * Ordinary constructor.
     * @param $prom_ar
     * @param $act_ar
     * @param $prize_ars
     */
    protected function __construct($prom_ar, $act_ar, $prize_ars)
    {
        parent::__construct($prom_ar);
        $this->_act_ar = $act_ar;
        foreach($prize_ars as $ar){
            $this->_prize_ars[$ar->prize_id] = $ar;
        }
    }

    /**
     * 获取对象
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
            $act_ar = ScratchAR::findOne(['prom_id'=>$prom_ar->prom_id]);
        }
        if(empty($prom_ar)) return null;

        $prize_ars = PrizeLister::getListEx($prom_ar->prom_id, $act_ar->activity_id, array(), 0, null, false, false);

        return new Scratch($prom_id, $prom_ar, $act_ar, $prize_ars);
    }

    /**
     * 获取具体类型活动的类别
     * @return int
     */
    public static function getType()
    {
        return PromConst::PROMOTION_TYPE_SCRATCH;
    }

    /**
     * 添加具体类型的活动
     * @param $prom_id
     * @param $activity_data
     * @param bool $get_id
     * @param $attr
     * @return ScratchAR|int|null
     */
    public static function addActivity($prom_id, $activity_data, $get_id=false, $attr=null)
    {
        $scratch_activity_ar = new ScratchAR();

        $scratch_activity_ar->setAttributes($activity_data['activity']);
        $scratch_activity_ar->prom_id = $prom_id;

        if($scratch_activity_ar->validate() && $scratch_activity_ar->insert()){
            if(!self::_addPrizesAndSns($prom_id, $scratch_activity_ar->activity_id, $activity_data['prizes'])) return null;
            return $get_id ? $scratch_activity_ar->activity_id : $scratch_activity_ar;
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
            foreach($this->_prize_ars as $key => $prize_ar){
                $act_data['prizes'][$key] = $prize_ar->toArray();
            }
        }else{
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

        return $this->_act_ar->validate() && $this->_act_ar->save();
    }

    /**
     * 添加奖项及SN码
     * @param $prom_id
     * @param $act_id
     * @param $prizes
     * @return bool|int
     */
    private static function _addPrizesAndSns($prom_id, $act_id, $prizes)
    {
        $prize_count=0;
        foreach($prizes as $prize){
            //添加奖项
            $prize_data = self::_generatePrizeData($prize);
            $prize_obj = PrizeObj::createPrize($prom_id, $act_id, $prize_data);
            if(empty($prize_obj)){
                return false;
            }
            $prize_id = $prize_obj->getInfo()['prize_id'];

            //为该奖项添加SN码
            $sn_count = 0;
            while($sn_count < $prize['num']){
                $sn_data = self::_generateSnData($prom_id,$act_id,$prize_id,$prize['prize_type'],$prize['title'], $prize['name']);
                $sn_obj = SnObj::createSn($prom_id, $act_id, $sn_data);

                if(empty($sn_obj)){
                    return false;
                }
                $sn_count++;
            }
            $prize_count++;
        }

        return $prize_count;
    }

    /**
     * 生成奖品数据，用于添加到数据库
     * @param $prize_data
     * @return mixed
     */
    private static function _generatePrizeData($prize_data)
    {
        //填入其他SN码信息
        $prize_info['prize_type'] = $prize_data['prize_type'];
        $prize_info['title'] = $prize_data['title'];
        $prize_info['name'] = $prize_data['name'];
        $prize_info['num'] = $prize_data['num'];
        $prize_info['point'] = $prize_data['point'];

        return $prize_info;
    }

    /**
     * 生成SN码数据，用于添加到数据库
     * @param $prom_id
     * @param $activity_id
     * @param $prize_id
     * @param $prize_type
     * @param $prize_title
     * @param $prize_name
     * @return mixed
     */
    private static function _generateSnData($prom_id,$activity_id,$prize_id,$prize_type, $prize_title, $prize_name)
    {
        $sn_info['prom_id'] = $prom_id;
        $sn_info['activity_id'] = $activity_id;
        $sn_info['prize_id'] = $prize_id;
        $sn_info['prize_type'] = $prize_type;
        $sn_info['prize_title'] = $prize_title;
        $sn_info['prize_name'] = $prize_name;

        return $sn_info;
    }
}