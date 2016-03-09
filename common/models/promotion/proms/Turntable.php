<?php

namespace common\models\promotion\proms;

use common\models\promotion\PromConst;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivityTurntable as TurntableAR;
use common\models\promotion\common\sn\SnObj;

/**
 * 大转盘活动
 * Class Turntable
 * @package common\models\promotion\proms
 */
Class Turntable extends Promotion
{
    /**
     * 子活动AR
     * @var TurntableAR
     */
    protected $_act_ar;

    /**
     * 构造函数
     * Ordinary constructor.
     * @param null $prom_ar
     * @param null $act_ar
     */
    protected function __construct($prom_ar, $act_ar)
    {
        parent::__construct($prom_ar);
        $this->_act_ar = $act_ar;
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
            $act_ar = TurntableAR::findOne(['prom_id'=>$prom_ar->prom_id]);
        }
        if(empty($act_ar)) return null;

        return new TurntableAR($prom_ar, $act_ar);
    }

    /**
     * 获取具体类型活动的类别
     * @return int
     */
    public static function getType()
    {
        return PromConst::PROMOTION_TYPE_TURNTABLE;
    }

    /**
     * 添加具体类型的活动
     * @param $prom_id
     * @param $activity_data
     * @param bool $get_id
     * @param $attr
     * @return TurntableAR|int|null
     */
    public static function addActivity($prom_id, $activity_data, $get_id=false, $attr=null)
    {
        $turntable_activity_ar = new TurntableAR();

        $turntable_activity_ar->setAttributes($activity_data);
        $turntable_activity_ar->prom_id = $prom_id;

        if($turntable_activity_ar->validate() && $turntable_activity_ar->insert()){
            if(!self::_addSns($prom_id, $turntable_activity_ar->activity_id, $activity_data)) return null;
            return $get_id ? $turntable_activity_ar->activity_id : $turntable_activity_ar;
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
        return $get_array ? $this->_act_ar->toArray() : $this->_act_ar;
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
        unset($activity_data['prom_id']);
        unset($activity_data['activity_id']);
        unset($activity_data['num']);
        $this->_act_ar->setAttributes($activity_data);

        return $this->_act_ar->validate() && $this->_act_ar->save();
    }


    /**
     * 为各个奖项添加SN码
     * @param $prom_id
     * @param $act_id
     * @param $activity_data
     * @return int
     */
    private static function _addSns($prom_id, $act_id, $activity_data)
    {
        $first_prize_title = $activity_data['first_prize_title'];
        $first_prize_name = $activity_data['first_prize_name'];
        $first_prize_num = $activity_data['first_prize_num'];
        $second_prize_title = $activity_data['second_prize_title'];
        $second_prize_name = $activity_data['second_prize_name'];
        $second_prize_num = $activity_data['second_prize_num'];
        $third_prize_title = $activity_data['third_prize_title'];
        $third_prize_name = $activity_data['third_prize_name'];
        $third_prize_num = $activity_data['third_prize_num'];

        //添加一等奖SN码
        $count1=0;
        while($count1 < $first_prize_num){
            $sn_data = self::_generateSnData($prom_id, $act_id, 1, $first_prize_title, $first_prize_name);

            $sn_obj = SnObj::createSn($prom_id,$act_id, $sn_data);
            if(empty($sn_obj)){
                return false;
            }
            $count1++;
        }

        //添加二等奖SN码
        $count2=0;
        while($count2 < $second_prize_num){
            $sn_data = self::_generateSnData($prom_id,$act_id,2,$second_prize_title, $second_prize_name);

            $sn_obj = SnObj::createSn($prom_id,$act_id, $sn_data);
            if(empty($sn_obj)){
                return false;
            }
            $count2++;
        }

        //添加三等奖SN码
        $count3=0;
        while($count3 < $third_prize_num){
            $sn_data = self::_generateSnData($prom_id,$act_id,3,$third_prize_title, $third_prize_name);

            $sn_obj = SnObj::createSn($prom_id,$act_id, $sn_data);
            if(empty($sn_obj)){
                return false;
            }
            $count3++;
        }
        return $count1 + $count2 + $count3;
    }

    /**
     * 生成并组装SN码数据
     * @param $prom_id
     * @param $activity_id
     * @param $prize_type
     * @param $prize_title
     * @param $prize_name
     * @return mixed
     */
    private static function _generateSnData($prom_id, $activity_id, $prize_type, $prize_title, $prize_name)
    {

        //填入其他SN码信息
        $sn_info['prom_id'] = $prom_id;
        $sn_info['activity_id'] = $activity_id;
        $sn_info['prize_id'] = 0; //大转盘prize_id均为0，因未用到promotion_prize表
        $sn_info['prize_type'] = $prize_type;
        $sn_info['prize_title'] = $prize_title;
        $sn_info['prize_name'] = $prize_name;

        return $sn_info;
    }
}