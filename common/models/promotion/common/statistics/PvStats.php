<?php

namespace common\models\promotion\common\statistics;

use yii\base\Exception;
use common\models\ar\PromotionPv;

/**
 * 页面访问统计
 * Class PvStats
 * @package common\models\promotion\statistics
 */
class PvStats
{
    /**
     * 访问数据AR记录
     * @var array|PromotionPv|null|\yii\db\ActiveRecord
     */
    protected $_pv_ar;

    /**
     * 构造函数
     * PvStats constructor.
     * @param $prom_id
     * @param $activity_id
     */
    public function __construct($prom_id, $activity_id)
    {
        if(!empty($pv_ar)){
            $this->_pv_ar = $pv_ar;
        }else{
            $pv_total_ar = PromotionPv::find()->where(['prom_id'=>$prom_id, 'activity_id'=>$activity_id])->one();
            if(empty($pv_total_ar)){
                $pv_total_ar = new PromotionPv();
                $pv_total_ar->prom_id = $prom_id;
                $pv_total_ar->activity_id = $activity_id;
                if(!$pv_total_ar->save()) $pv_total_ar=null;
            }
            $this->_pv_ar = $pv_total_ar;
        }
    }

    /**
     * 更新Pv值
     * @param null $from_data
     * @return bool
     */
    public function updatePv($from_data=null)
    {
        if(empty($this->_pv_ar)) return false;
        try{
            $total_up = isset($from_data['total_views']) ? (int)$from_data['total_views'] : 1;
            $this->_pv_ar->updateCounters(['total_views' => $total_up]);
            if(isset($from_data['from_1'])) $this->_pv_ar->updateCounters(['from_1'=>(int)$from_data['from_1']]);
            if(isset($from_data['from_2'])) $this->_pv_ar->updateCounters(['from_2'=>(int)$from_data['from_2']]);
            if(isset($from_data['from_3'])) $this->_pv_ar->updateCounters(['from_3'=>(int)$from_data['from_3']]);

            if(isset($from_data['details'])){
                $this->_pv_ar->details = $from_data['details'];
                return $this->_pv_ar->save();
            }
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * 获取Pv值
     * @return int|mixed
     */
    public function getPv()
    {
        if(empty($this->_pv_ar)) return 0;
        return $this->_pv_ar->total_views;
    }

    /**
     * 批量统计Pv值
     * @param $prom_id
     * @param null $activity_id
     * @return mixed
     */
    public static function getPvEx($prom_id, $activity_id=null)
    {
        $query = PromotionPv::find()->where(['prom_id'=>$prom_id]);
        if($activity_id!==null) $query->andWhere(['activity_id'=>(int)$activity_id]);

        return $query->sum('total_views');
    }

    /**
     * 获取详细PV信息
     * @return array|null
     */
    public function getPvInfo()
    {
        if(empty($this->_pv_ar)) return null;
        return $this->_pv_ar->toArray();
    }
}