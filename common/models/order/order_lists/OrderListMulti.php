<?php

namespace common\models\order\order_lists;

use common\models\ar\Order as OrderSearchAR;
use common\models\order\OrderConst;

/**
 * 多种类型订单列表类
 *
 * 注：本列表暂不支持子类型搜索，否则分页将出现问题
 *
 * 搜索项:
 *      order_no
 *      order_type
 *      order_status
 *      type
 *      apply_name
 *      apply_phone
 *      apply_arrive_date
 *      order_no_arr（内部使用）
 *      status_set_arr（内部使用）
 *      status_unset_arr（内部使用）
 *
 * Class AllOrderList
 * @package common\models\order\order_lists
 */
class OrderListMulti implements IOrderList //观察者
{
    /**
     * 集团ID
     * @var int
     */
    protected $_g_id;

    /**
     * 门店ID
     * @var int
     */
    protected $_h_id;
    
    /**
     * 列表对象数组
     * @var array
     */
    protected $_observers = array();

    /**
     * 订单类型
     * @var array
     */
    protected $_types = array();

    /**
     * 构造函数
     * OrderList constructor.
     * @param $group_id
     * @param $hotel_id
     */
    public function __construct($group_id, $hotel_id)
    {
        $this->_g_id = (int)$group_id;
        $this->_h_id = (int)$hotel_id;
    }

    /**
     * 获取类型
     * @return array
     */
    public static function getType()
    {
        return ['type'=>0];
    }

    /**
     * 添加列表对象
     * @param OrderList $lister
     */
    public function addLister(OrderList $lister)
    {
        $this->_observers[] = $lister;
        $this->_types[] = $lister->getType()['type'];
    }

    /**
     * 移除列表对象
     * @param OrderList $lister
     * @return bool
     */
    public function removeLister(OrderList $lister)
    {
        $index = array_search($lister, $this->_observers);
        if ($index === false || ! array_key_exists($index, $this->_observers)) {
            return false;
        }
        unset($this->_observers[$index]);
        return TRUE;
    }

    /**
     * 获取订单搜索表信息
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @return array|int|string|\yii\db\ActiveRecord[]
     */
    public function getSearchList($search_data,$offset,$limit,$get_count=false)
    {
        $query = OrderSearchAR::find()->select('*');

        //有索引搜索项
        if($this->_g_id>0) {
            $query->where(['g_id'=>$this->_g_id]);
            if($this->_h_id>0) $query->andWhere(['g_id'=>$this->_g_id]);
        }elseif($this->_h_id>0) {
            $query->where(['h_id'=>$this->_h_id]);
        }else{
            return $get_count ? 0 : null;
        }
        if(!empty($search_data['order_no'])) $query->andWhere(['order_no'=>$search_data['order_no']]);
        if(!empty($search_data['member_id'])) $query->andWhere(['member_id'=>$search_data['member_id']]);
        if(!empty($search_data['apply_phone'])) $query->andWhere(['apply_phone'=>$search_data['apply_phone']]);

        if(!empty($search_data['order_status'])){
            $status_set_arr = (isset($search_data['status_set_arr']) && is_array($search_data['status_set_arr'])) ? $search_data['status_set_arr'] : array();
            $status_unset_arr = (isset($search_data['status_unset_arr']) && is_array($search_data['status_unset_arr'])) ? $search_data['status_unset_arr'] : array();

            $search_status_arr = utils\QueryBuilder::parseStatusName($search_data['order_status']);
            $search_data['status_set_arr'] = array_merge($search_status_arr['status_set_arr'], $status_set_arr);
            $search_data['status_unset_arr'] = array_merge($search_status_arr['status_unset_arr'], $status_unset_arr);
        }
        if( !empty($search_data['status_set_arr']) || !empty($search_data['status_unset_arr']) ){
            $status_set_arr = !empty($search_data['status_set_arr']) ? $search_data['status_set_arr'] : array();
            $status_unset_arr = !empty($search_data['status_unset_arr']) ? $search_data['status_unset_arr'] : array();
            $condition = utils\QueryBuilder::getStatusQueryCondition($status_set_arr, $status_unset_arr, 'order_status');
            $query->andWhere($condition);
        }

        //无索引搜索项
        if(!empty($search_data['apply_name'])) $query->andWhere(['apply_name'=>$search_data['apply_name']]);
        if(!empty($search_data['pay_type'])) $query->andWhere(['pay_type'=>$search_data['pay_type']]);
        if(!empty($search_data['order_source'])) $query->andWhere(['order_source'=>$search_data['order_source']]);

        //获取结果
        if($get_count){
            $count = $query->offset($offset)->limit($limit)->count();
            return $count;
        }else {
            $list = $query->orderBy('add_time DESC')->offset($offset)->limit($limit)->asArray()->all();
            return $list;
        }
    }

    /**
     * 获取所有给定类型的待处理订单列表
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return array|null
     */
    public function getPendingList($search_data,$offset,$limit,$get_count=false, $with_product_type=true)
    {
        $search_data['status_unset_arr'] = [ OrderConst::STATUS_BIT_DEALT_CANCELED, OrderConst::STATUS_BIT_DEALT_SUCCESS ];

        return $this->getList($search_data, $offset, $limit, $get_count, $with_product_type);
    }

    /**
     * 获取所有给定类型的无效订单列表
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return array|null
     */
    public function getInvalidList($search_data, $offset, $limit, $get_count=false, $with_product_type=true)
    {
        $search_data['status_set_arr'] = [ OrderConst::STATUS_BIT_DEALT_CANCELED ];

        return $this->getList($search_data, $offset, $limit, $get_count, $with_product_type);
    }

    /**
     * 获取所有给定类型的订单列表
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return array|null
     */
    public function getList($search_data,$offset,$limit,$get_count=false, $with_product_type=true)
    {
        $search_list = $this->getSearchList($search_data, $offset, $limit, $get_count);
        $list_array = $this->_getOrderNoAndTypes($search_list);
        $order_type_arr = $list_array['order_type_arr'];
        $new_search_data = ['order_no_arr'=>$list_array['order_no_arr']];

        $lists = array();
        if(!empty($this->_observers)){
            foreach($this->_observers as $observer){
                if(in_array($observer->getType()['type'], $order_type_arr)) {
                    $lists[] = $observer->getList($new_search_data, $offset, $limit, $get_count, $with_product_type);
                }
            }
        }else{
            return null;
        }

        return $this->_mergeLists($lists);
    }

    /**
     * 获取订单号列表
     * @param $order_list
     * @return mixed
     */
    protected function _getOrderNoAndTypes($order_list)
    {
        $order_no_arr = array();
        $order_type_arr = array();
        foreach($order_list as $order){
            $order_no_arr[] = $order['order_no'];
            $order_type_arr[] = $order['product_type'];
        }
        $result['order_no_arr'] = $order_no_arr;
        $result['order_type_arr'] = $order_type_arr;

        return $result;
    }

    /**
     * 对订单列表进行融合
     * @param $lists_arr
     * @return array
     */
    protected function _mergeLists($lists_arr)
    {
        if(empty($lists_arr)) return null;

        $merged_list = array();
        $i = $j = 0;
        foreach($lists_arr as $list){
            foreach($list as $key=>$item){
                $add_time = isset($item['add_time']) ? $item['add_time'] : $key; //如果能从列表行中直接获取时间，则直接使用添加时间；若无法获取到，则从键名中获取。
                $unique_sort_key = $add_time.'.'.$i.$j;
                $merged_list[$unique_sort_key] = $item;
                $j++;
            }
            $i++;
        }
        krsort($merged_list);

        return $merged_list;
    }
}
