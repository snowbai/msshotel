<?php

namespace common\models\promotion\common\sn;

use common\models\ar\PromotionSn;
use common\models\promotion\PromConst;

/**
 * SN管理器器
 * Class SnLister
 * @package common\models\promotion\sn
 */
class SnLister
{
    /**
     * 活动ID
     * @var
     */
    protected $_prom_id;

    /**
     * 门店ID
     * @var
     */
    protected $_activity_id;

    /**
     * 构造函数
     * SnManager constructor.
     * @param $prom_id
     * @param $activity_id
     */
    public function __construct($prom_id, $activity_id)
    {
        $this->_prom_id = $prom_id;
        $this->_activity_id = $activity_id;
    }

    /**
     * 获取活动SN列表
     * @param $search_data
     * @param int $offset
     * @param null $limit
     * @param bool $get_count
     * @return array|int|string|\yii\db\ActiveRecord[]
     */
    public function getList($search_data, $offset=0, $limit=null, $get_count=false)
    {
        return $this->getListEx($this->_prom_id, $this->_activity_id, $search_data, $offset, $limit, $get_count);
    }

    /**
     * 获取SN统计信息
     * @return SnLister
     */
    public function getStatistics()
    {
        return $this-self::getStatisticsEx($this->_prom_id, $this->_activity_id);
    }

    /**
     * 获取活动SN列表
     * @param $prom_id
     * @param $activity_id
     * @param $search_data
     * @param int $offset
     * @param null $limit
     * @param bool $get_count
     * @return array|int|string|\yii\db\ActiveRecord[]
     */
    public static function getListEx($prom_id, $activity_id, $search_data, $offset=0, $limit=null, $get_count=false)
    {
        $query = PromotionSn::find()->where(['prom_id'=>$prom_id]);

        if(!empty($activity_id) && $activity_id>0){
            $query->andWhere(['activity_id' => $activity_id]);
        }
        if(isset($search_data['sn_no']) && !empty($search_data['sn_no'])) $query->andWhere(['sn_no' => $search_data['sn_no']]);
        if(isset($search_data['phone']) && !empty($search_data['phone'])) $query->andWhere(['phone' => $search_data['phone']]);
        if(isset($search_data['prize_type'])) $query->andWhere(['prize_type' => $search_data['prize_type']]);
        if(isset($search_data['status'])){
            if(is_array($search_data['status'])){
                $query->andWhere(['IN', 'status', $search_data['status']]);
            }else{
                $query->andWhere(['status' => $search_data['status']]);
            }
        }
        if(isset($search_data['notify_status'])){
            if(is_array($search_data['notify_status'])){
                $query->andWhere(['IN', 'notify_status', $search_data['notify_status']]);
            }else{
                $query->andWhere(['notify_status' => $search_data['notify_status']]);
            }
        }

        if($get_count==true){
            return $query->count();
        }

        if(isset($search_data['status_order']) && is_array($search_data['status_order'])){
            $status_order = $search_data['status_order'];
        }else{
            $status_order = [
                PromConst::SN_STATUS_GOT,
                PromConst::SN_STATUS_EXCHANGED,
                PromConst::SN_STATUS_ALLOCATED,
                PromConst::SN_STATUS_PENDING,
                PromConst::SN_STATUS_VACANT,
            ];
        }
        $status_order_str = implode(', ', $status_order);
        $query->OrderBy("FIELD(`status`, $status_order_str)");
        $query->addOrderBy(['notify_status'=>SORT_ASC,'activity_id'=>SORT_ASC,'prize_type'=>SORT_ASC]);

        $sn_list = $query->offset($offset)->limit($limit)->asArray()->all();
        return $sn_list;
    }

    /**
     * 获取SN统计信息
     * @param $prom_id
     * @param $activity_id
     * @param $use_prize_type
     * @return array|null
     */
    public static function getStatisticsEx($prom_id, $activity_id, $use_prize_type=false)
    {
        $query = PromotionSn::find()->select('sn_no, prize_type, prize_id, status, notify_status')->where(['prom_id'=>$prom_id]);

        if($activity_id!==null){
            $query->andWhere(['activity_id' => (int)$activity_id]);
        }

        $result = $query->asArray()->all();

        return self::_doCount($result, $use_prize_type);
    }

    /**
     * 计算能统计信息
     * @param $list
     * @param $use_prize_type
     * @return array|null
     */
    protected static function _doCount($list, $use_prize_type)
    {
        if(!is_array($list)) return null;

        $prize_statistic = array();
        $total_count = 0;
        $status_count = array();
        $notify_count = array();
        foreach($list as $item){
            $cur_prize_id = $use_prize_type ? $item['prize_type'] : $item['prize_id'];
            $cur_status = $item['status'];
            $cur_notify_status = $item['notify_status'];

            if(!isset($prize_statistic[$cur_prize_id]['total'])) $prize_statistic[$cur_prize_id]['total'] = 0;
            if(!isset($prize_statistic[$cur_prize_id]['status'][$cur_status])) $prize_statistic[$cur_prize_id]['status'][$cur_status] = 0;
            if(!isset($prize_statistic[$cur_prize_id]['notify'][$cur_notify_status])) $prize_statistic[$cur_prize_id]['notify'][$cur_notify_status] = 0;
            $prize_statistic[$cur_prize_id]['total']++;
            $prize_statistic[$cur_prize_id]['status'][$cur_status]++;
            $prize_statistic[$cur_prize_id]['notify'][$cur_notify_status]++;

            //所有奖项统计信息
            if(!isset($status_count[$cur_status])) $status_count[$cur_status] = 0;
            if(!isset($status_count[$cur_notify_status])) $status_count[$cur_notify_status] = 0;
            $status_count[$cur_status]++;
            $notify_count[$cur_notify_status]++;
            $total_count++;
        }

        return ['total'=>$total_count, 'status'=>$status_count, 'notify'=>$notify_count, 'prizes_statistic'=>$prize_statistic];
    }
}