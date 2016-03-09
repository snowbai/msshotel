<?php

namespace backend\models\promotion\proms_list;

use common\models\base\Constant;
use common\models\ar\Promotion as PromotionAR;

/**
 * 促销活动列表类
 * Class Promotion
 * @package backend\models\Promotion
 */
abstract class PromotionList
{
    /**
     * 集团ID
     * @var
     */
    protected $_g_id;

    /**
     * 酒店ID
     * @var
     */
    protected $_h_id;

    /**
     * 构造函数（添加活动时不需要传递prom_id，其他情况需传递prom_id）
     * @param $g_id
     * @param $h_id
     */
    public function __construct($g_id,$h_id)
    {
        $this->_g_id = $g_id;
        $this->_h_id = $h_id;
    }

    /**
     * 获取活动列表
     * @param string $prom_name
     * @param string $prom_start_time
     * @param string $prom_end_time
     * @param int $prom_status
     * @return mixed
     */
    abstract public function getList($prom_name='', $prom_start_time='', $prom_end_time='', $prom_status=-1);

    /**
     * 屏蔽活动
     * @param $prom_id
     * @return bool
     */
    final public function block($prom_id)
    {
        return $this->_updateStatus($prom_id, Constant::STATUS_BLOCKED);
    }

    /**
     * 删除活动
     * @param $prom_id
     * @return bool
     */
    final public function delete($prom_id)
    {
        return $this->_updateStatus($prom_id, Constant::STATUS_DELETED);
    }

    /**
     * 取消屏蔽
     * @param $prom_id
     * @return bool
     */
    final public function clearStatus($prom_id)
    {
        return $this->_updateStatus($prom_id, 0);
    }

    final public function orderUp(){}

    /**
     * 更新状态
     * @param $prom_id
     * @param $status
     * @return bool
     */
    protected function _updateStatus($prom_id, $status)
    {
        $promotion_ar = PromotionAR::find()->where(['prom_id'=>$prom_id,'g_id'=>$this->_g_id,'h_id'=>$this->_h_id])->one();

        if(empty($promotion_ar)){
            return false;
        }
        $promotion_ar->status = $status;

        return $promotion_ar->save();
    }

}