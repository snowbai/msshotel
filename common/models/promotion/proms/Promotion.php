<?php

namespace common\models\promotion\proms;

use common\models\base\Constant;
use yii\base\Exception;
use common\models\ar\Promotion as PromotionAR;

/**
 * 促销活动模版类
 * Class Promotion
 * @package common\models\promotion\proms
 */
abstract class Promotion implements IPromotion//模版
{
    /**
     * 活动ID
     * @var PromotionAR
     */
    protected $_prom_ar;

    /**
     * 构造函数
     * Promotion constructor.
     * @param $prom_ar
     */
    protected function __construct($prom_ar)
    {
        $this->_prom_ar = $prom_ar;
    }

    /**
     * 创建新活动
     * @param $group_id
     * @param $hotel_id
     * @param $prom_data
     * @param $act_data
     * @return null
     * @throws Exception
     */
    public static function create($group_id, $hotel_id, $prom_data, $act_data)
    {
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try
        {
            $prom_type = static::getType();
            $prom_ar = static::_addPromotion($group_id, $hotel_id, $prom_type, $prom_data);
            if(empty($prom_ar)){
                throw new Exception('创建活动基本信息失败');
            }
            $act_ars = static::addActivity($prom_ar->prom_id, $act_data);
            if(empty($act_ars)){
                throw new Exception('创建子活动信息失败');
            }
            $transaction->commit();
            return static::getInstance(0, $prom_ar, $act_ars);
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            return null;
        }
    }

    /**
     * 获取活动ID
     * @return int
     */
    public function getId()
    {
        if(empty($this->_prom_ar)) return 0;
        else return $this->_prom_ar->prom_id;
    }

    /**
     * 获取活动信息
     * @return array|null
     */
    public function getInfo()
    {
        if(empty($this->_prom_ar)) return null;
        $info['promotion'] = $this->_prom_ar->toArray();
        $info['activity'] = $this->getActivity(true);

        return $info;
    }

    /**
     * 更新活动信息
     * @param $prom_data
     * @param $act_data
     * @return bool
     * @throws Exception
     */
    public function update($prom_data, $act_data)
    {
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try
        {
            if(!$this->updateInfo($prom_data)){
                throw new Exception('更新活动基本信息失败');
            }
            if(!$this->updateActivity($act_data)){
                throw new Exception('更新子活动失败');
            }

            $transaction->commit();
            return true;
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * 更新促销活动基本信息（操作公用表）
     * @param $promotion_data
     * @return bool
     */
    public function updateInfo($promotion_data)
    {
        if(empty($this->_prom_ar)) return false;

        unset($promotion_data['prom_id']);
        unset($promotion_data['prom_type']);
        unset($promotion_data['g_id']);
        unset($promotion_data['h_id']);
        $this->_prom_ar->setAttributes($promotion_data);
        if ($this->_prom_ar->validate() && $this->_prom_ar->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 更新活动状态
     * @param $status
     * @return bool
     */
    public function updateStatus($status)
    {
        if(empty($this->_prom_ar)) return false;
        $this->_prom_ar->status = $status;
        return $this->_prom_ar->save();
    }

    /**
     * 屏蔽活动
     * @return bool
     */
    public function block(){
        return $this->updateStatus(Constant::STATUS_BLOCKED);
    }

    /**
     * 删除活动
     * @return bool
     */
    public function delete(){
        return $this->updateStatus(Constant::STATUS_DELETED);
    }

    /**
     * 取消屏蔽
     * @return bool
     */
    public function unblock(){
        return $this->updateStatus(Constant::STATUS_NORMAL);
    }

    /**
     * 添加促销活动基本信息（操作公用表）
     * @param $group_id
     * @param $hotel_id
     * @param $prom_type
     * @param $prom_data
     * @return PromotionAR
     * @throws Exception
     * @throws \Exception
     */
    private static function _addPromotion($group_id, $hotel_id, $prom_type, $prom_data)
    {
        $promotion_ar = new PromotionAR();

        $promotion_ar->setAttributes($prom_data);
        $promotion_ar->g_id = $group_id;
        $promotion_ar->h_id = $hotel_id;
        $promotion_ar->prom_type = $prom_type;
        $promotion_ar->list_order = self::_getNewListOrder($group_id, $hotel_id);
        if ($promotion_ar->validate() && $promotion_ar->insert() ) {
            return $promotion_ar;
        }else{
            return null;
        }
    }

    /**
     * 获取新的排序值
     * @param $group_id
     * @param $hotel_id
     * @return int|mixed
     */
    private static function _getNewListOrder($group_id, $hotel_id)
    {
        $max_list_order = PromotionAR::find()->where(['g_id'=>$group_id, 'h_id'=>$hotel_id])->max('list_order');
        if($max_list_order===null){
            $new_list_order = 0;
        }else{
            $new_list_order = $max_list_order + 1;
        }

        return $new_list_order;
    }
}