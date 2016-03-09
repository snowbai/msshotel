<?php

namespace common\models\promotion\proms;

use common\models\base\Constant;
use common\models\promotion\PromConst;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivitySeckill as SeckillAR;

/**
 * 秒杀活动类
 * Class Seckill
 * @package common\models\promotion\proms;
 */
class Seckill extends Promotion
{
    /**
     * 秒杀项AR
     * @var array|null
     */
    protected $_act_ars;

    /**
     * 构造函数
     * Ordinary constructor.
     * @param null $prom_ar
     * @param null $act_ars
     */
    protected function __construct($prom_ar, $act_ars)
    {
        parent::__construct($prom_ar);
        $this->_act_ars = $act_ars;
    }

    /**
     * 获取对象
     * @param $prom_id
     * @param $prom_ar
     * @param $act_ars
     * @return Ordinary|null
     */
    public static function getInstance($prom_id, $prom_ar=null, $act_ars=null)
    {
        if(empty($prom_ar)){
            $prom_ar = PromotionAR::findOne($prom_id);
        }
        if(empty($prom_ar)) return null;
        if(empty($act_ars)){
            $act_ars = SeckillAR::find()
                ->where(['prom_id'=>$prom_ar->prom_id])
                ->andWhere(['<>','status',Constant::STATUS_DELETED])
                ->orderBy('start_time ASC')->all();
        }
        if(empty($act_ars)) return null;

        return new Seckill($prom_ar, $act_ars);
    }

    /**
     * 获取具体类型活动的类别
     * @return int
     */
    public static function getType()
    {
        return PromConst::PROMOTION_TYPE_SECKILL;
    }

    /**
     * 添加具体类型的活动
     * @param $prom_id
     * @param $activity_data
     * @param bool $get_id
     * @param $attr
     * @return array|null
     */
    public static function addActivity($prom_id, $activity_data, $get_id=false, $attr=null)
    {
        $result = array();
        foreach($activity_data as $item_data){
            $seckill_activity_ar = new SeckillAR();

            $seckill_activity_ar->setAttributes($item_data);
            $seckill_activity_ar->prom_id = $prom_id;
            $seckill_activity_ar->remain_num = isset($item_data['num']) ? $item_data['num'] : 0;

            if($seckill_activity_ar->validate() && $seckill_activity_ar->insert()){
                $result[] = $get_id ? $seckill_activity_ar->activity_id : $seckill_activity_ar;
            }else{
                return null;
            }
        }
        return $result;
    }

    /**
     * 获取具体类型的活动信息
     * @param bool $get_array
     * @param $attr
     * @return array|null
     */
    public function getActivity($get_array=false, $attr=null)
    {
        if(is_numeric($attr)){
            return isset($this->_act_ars[$attr]) ?
                ( $get_array ? $this->_act_ars[$attr]->toArray() : $this->_act_ars[$attr])
                : null;
        }

        if($get_array==false) return $this->_act_ars;

        $result = array();
        foreach($this->_act_ars as $key=>$ar){
            $result[$key] = $ar->toArray();
        }
        return $result;
    }

    /**
     * 更新活动具体信息
     * @param $activity_data
     * @param $attr
     * @return bool
     */
    public function updateActivity($activity_data, $attr=null)
    {
        if(is_numeric($attr)){
            if(!isset($this->_act_ars[$attr])) return false;
            $act_ar = $this->_act_ars[$attr];
            unset($activity_data['prom_id']);
            unset($activity_data['num']);
            unset($activity_data['remain_num']);
            unset($activity_data['price']);
            unset($activity_data['start_time']);
            unset($activity_data['end_time']);
            $act_ar->setAttributes($activity_data);
            return $act_ar->validate() && $act_ar->save();
        }

        foreach($activity_data as $key=>$item_data){
            if(!isset($this->_act_ars[$key])) return false;
            $act_ar = $this->_act_ars[$key];
            unset($item_data['prom_id']);
            unset($item_data['num']);
            unset($item_data['remain_num']);
            unset($item_data['price']);
            unset($item_data['start_time']);
            unset($item_data['end_time']);
            $act_ar->setAttributes($item_data);
            if(!($act_ar->validate() && $act_ar->save())){
                return false;
            }
        }
        return true;
    }
}