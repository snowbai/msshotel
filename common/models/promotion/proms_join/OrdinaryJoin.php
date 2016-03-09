<?php

namespace common\models\promotion\proms_join;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivityOrdinary as OrdinaryAR;
use common\models\promotion\proms_join\common\DoCheck;

/**
 * 促销活动参与类
 * Class OrdinaryJoin
 * @package common\models\promotion\proms_join
 */
class OrdinaryJoin implements IPromotionJoin
{
    /**
     * 使用公用方法
     */
    use DoCheck;

    /**
     * 活动ID
     * @var PromotionAR
     */
    protected $_prom_ar;

    /**
     * 具体活动AR
     * @var OrdinaryAR
     */
    protected $_act_ar;

    /**
     * 构造函数
     * OrdinaryJoin constructor.
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
     * @return OrdinaryJoin|null
     */
    public static function getInstance($prom_id)
    {
        $prom_ar = PromotionAR::findOne((int)$prom_id);
        if(empty($prom_ar)) return null;
        $act_ar = OrdinaryAR::findOne($prom_id);
        if(empty($act_ar)) return null;

        return new OrdinaryJoin($prom_ar, $act_ar);
    }

    /**
     * 获取活动详情
     * @return mixed
     */
    public function getInfo()
    {
        $info['promotion'] = $this->_prom_ar->toArray();
        $info['activity'] = $this->_act_ar->toArray();
    }

    /**
     * 判断活动能否参加
     * @return bool
     */
    public function checkValid()
    {
        return $this->_checkPromValid($this->_prom_ar);
    }
}