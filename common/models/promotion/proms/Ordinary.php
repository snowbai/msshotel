<?php

namespace common\models\promotion\proms;

use common\models\promotion\PromConst;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivityOrdinary as OrdinaryAR;

/**
 * 普通促销活动类
 * Class Ordinary
 * @package common\models\promotion\proms
 */
class Ordinary extends Promotion
{
    /**
     * 子活动AR
     * @var null|static
     */
    protected $_act_ar;

    /**
     * 构造函数
     * Ordinary constructor.
     * @param $prom_ar
     * @param $act_ar
     */
    protected function __construct($prom_ar, $act_ar)
    {
        parent::__construct($prom_ar);
        $this->_act_ar = $act_ar;
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
            $act_ar = OrdinaryAR::findOne(['prom_id'=>$prom_ar->prom_id]);
        }
        if(empty($act_ar)) return null;

        return new Ordinary($prom_ar, $act_ar);
    }

    /**
     * 获取具体类型活动的类别
     * @return int
     */
    public static function getType()
    {
        return PromConst::PROMOTION_TYPE_ORDINARY;
    }

    /**
     * 添加具体类型的活动
     * @param $prom_id
     * @param $activity_data
     * @param bool $get_id
     * @param $attr
     * @return OrdinaryAR|int|null
     */
    public static function addActivity($prom_id, $activity_data, $get_id=false, $attr=null)
    {
        $ordinary_activity_ar = new OrdinaryAR();

        $ordinary_activity_ar->setAttributes($activity_data);
        $ordinary_activity_ar->prom_id = $prom_id;
        $ordinary_activity_ar->remain_num = isset($activity_data['num']) ? $activity_data['num'] : 0;

        if($ordinary_activity_ar->validate() && $ordinary_activity_ar->insert()){
            return $get_id ? $ordinary_activity_ar->activity_id : $ordinary_activity_ar;
        }else{
            return $get_id ? 0 : null;
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
        unset($activity_data['remain_num']);
        $this->_act_ar->setAttributes($activity_data);

        return $this->_act_ar->validate() && $this->_act_ar->save();
    }
}