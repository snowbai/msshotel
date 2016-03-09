<?php

namespace common\models\order\order_lists;

use common\models\ar\OrderPromotion as PromotionOrderAR;
use common\models\order\OrderConst;

/**
 * 普通活动订单列表类
 * Class PromotionOrderList
 * @package common\models\order\order_lists
 */
class PromotionOrderList extends OrderList
{
    /**
     * 获取类型
     * @return array
     */
    public static function getType()
    {
        return ['type'=>OrderConst::ORDER_PROMOTION];
    }

    /**
     * 获取订单列表
     * @param $valid_search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return array|int|string|\yii\db\ActiveRecord[]
     */
    public function _getList($valid_search_data, $offset, $limit, $get_count=false, $with_product_type=false)
    {
        $query = PromotionOrderAR::find()->select('*');
        //是否携带产品类型
        if ($with_product_type) {
            $query->addSelect(["CONCAT(".OrderConst::ORDER_PROMOTION.") as product_type"])->addSelect('prom_type as product_subtype');
        }

        //内部使用
        if (isset($valid_search_data['order_no_arr'])) {
            $query->andWhere(['IN', 'order_no', $valid_search_data['order_no_arr']]);
            //获取结果
            if ($get_count) {
                $count = $query->count();
                return $count;
            } else {
                $list = $query->orderBy('add_time DESC')->asArray()->all();
                return $list;
            }
        }

        //有索引搜索项
        if ($this->_g_id > 0) {
            $query->where(['g_id' => $this->_g_id]);
            if ($this->_h_id > 0) $query->andWhere(['h_id' => $this->_h_id]);
        } elseif ($this->_h_id > 0) {
            $query->where(['h_id' => $this->_h_id]);
        } else {
            return $get_count ? 0 : null;
        }
        if ($this->_subtype !== null) $query->andWhere(['prom_type' => $this->_subtype]);
        if (!empty($valid_search_data['order_no'])) $query->andWhere(['order_no' => $valid_search_data['order_no']]);
        if (!empty($valid_search_data['member_id'])) $query->andWhere(['member_id' => $valid_search_data['member_id']]);
        if (!empty($valid_search_data['apply_phone'])) $query->andWhere(['apply_phone' => $valid_search_data['apply_phone']]);
        if (!empty($valid_search_data['from_date'])) $query->andWhere(['>=', 'add_time', date('Y-m-d', strtotime($valid_search_data['from_date']))]);
        if (!empty($valid_search_data['to_date'])) $query->andWhere(['<', 'add_time', date('Y-m-d', strtotime($valid_search_data['to_date'] . ' +1 day'))]);
        if (!empty($valid_search_data['status_set_arr']) || !empty($valid_search_data['status_unset_arr'])) {
            $status_set_arr = !empty($valid_search_data['status_set_arr']) ? $valid_search_data['status_set_arr'] : array();
            $status_unset_arr = !empty($valid_search_data['status_unset_arr']) ? $valid_search_data['status_unset_arr'] : array();
            $condition = utils\QueryBuilder::getStatusQueryCondition($status_set_arr, $status_unset_arr, 'order_status');
            $query->andWhere($condition);
        }

        //无索引搜索项
        if (!empty($valid_search_data['prom_type'])) $query->andWhere(['prom_type' => $valid_search_data['prom_type']]);
        if (!empty($valid_search_data['apply_arrive_date'])) $query->andWhere(['apply_arrive_date' => $valid_search_data['apply_arrive_date']]);
        if (!empty($valid_search_data['apply_leave_date'])) $query->andWhere(['apply_leave_date' => $valid_search_data['apply_leave_date']]);

        if (!empty($valid_search_data['apply_name'])) $query->andWhere(['apply_name' => $valid_search_data['apply_name']]);
        if (!empty($valid_search_data['pay_type'])) $query->andWhere(['pay_type' => $valid_search_data['pay_type']]);
        if (!empty($valid_search_data['order_source'])) $query->andWhere(['order_source' => $valid_search_data['order_source']]);

        //获取结果
        if ($get_count) {
            $count = $query->offset($offset)->limit($limit)->count();
            return $count;
        } else {
            $list = $query->orderBy('add_time DESC')->offset($offset)->limit($limit)->asArray()->all();
            return $list;
        }
    }
}