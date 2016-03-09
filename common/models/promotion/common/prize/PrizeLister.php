<?php

namespace common\models\promotion\common\prize;

use common\models\ar\PromotionPrize;

/**
 * 奖品管理器
 * Class PrizeLister
 * @package common\models\promotion\prize
 */
class PrizeLister
{
    /**
     * 活动ID
     * @var
     */
    protected $_prom_id;

    /**
     * 子活动ID
     * @var
     */
    protected $_activity_id;

    /**
     * 构造函数
     * @param $prom_id
     * @param $activity_id
     */
    public function __construct($prom_id,$activity_id)
    {
        $this->_prom_id = (int)$prom_id;
        $this->_activity_id = (int)$activity_id;
    }

    /**
     * 获取奖品列表
     * @param $search_data
     * @param int $offset
     * @param null $limit
     * @param $get_count
     * @param bool $get_array
     * @return array|int
     */
    public function getList($search_data, $offset=0, $limit=null, $get_count=false, $get_array=true)
    {
        return $this->getListEx($this->_prom_id, $this->_activity_id, $search_data, $offset, $limit, $get_count, $get_array);
    }

    /**
     * 获取奖品列表
     * @param $prom_id
     * @param $activity_id
     * @param $search_data
     * @param int $offset
     * @param null $limit
     * @param bool $get_count
     * @param bool $get_array
     * @return array|int|string|\yii\db\ActiveRecord[]
     */
    public static function getListEx($prom_id, $activity_id, $search_data, $offset=0, $limit=null, $get_count=false, $get_array=true)
    {
        $query = PromotionPrize::find()->where(['prom_id'=>$prom_id, 'activity_id'=>$activity_id]);
        if($activity_id!==null){
            $query->andWhere(['activity_id'=>$activity_id]);
        }
        if(isset($search_data['prize_type'])){
            $query->andWhere(['prize_type'=>$search_data['prize_type']]);
        }
        if(isset($search_data['status'])){
            if(is_array($search_data['status'])){
                $query->andWhere(['IN', 'status', $search_data['status']]);
            }else{
                $query->andWhere(['status' => $search_data['status']]);
            }
        }

        if($get_count) return $query->count();
        $query->orderBy('prize_type ASC')->offset($offset)->limit($limit);
        if($get_array){
            $query->asArray();
        }
        $prize_list = $query->all();
        return $prize_list;
    }
}