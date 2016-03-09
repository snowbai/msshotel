<?php

namespace common\models\order\order_lists;

use common\models\order\OrderConst;

/**
 * 订单列表模版类
 *
 * 搜索项:
 *      order_no
 *      order_type
 *      order_status
 *      from_date
 *      to_date
 *      type
 *      apply_name
 *      apply_phone
 *      apply_arrive_date
 *      order_no_arr（内部使用）
 *      status_set_arr（内部使用）
 *      status_unset_arr（内部使用）
 *
 * Class OrderList
 * @package common\models\order\order_lists
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
abstract class OrderList implements IOrderList //模版
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
     * 子类型
     * @var
     */
    protected $_subtype;

    /**
     * 构造函数
     * OrderList constructor.
     * @param $group_id
     * @param $hotel_id
     * @param $subtype
     */
    public function __construct($group_id, $hotel_id, $subtype=null)
    {
        $this->_g_id = (int)$group_id;
        $this->_h_id = (int)$hotel_id;
        $this->_subtype = $subtype;
    }

    /**
     * 获取待处理订单列表
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return mixed
     */
    public function getPendingList($search_data, $offset, $limit, $get_count=false, $with_product_type=false)
    {
        $search_data['status_unset_arr'] = [ OrderConst::STATUS_BIT_DEALT_CANCELED, OrderConst::STATUS_BIT_DEALT_SUCCESS ];

        return $this->getList($search_data, $offset, $limit, $get_count, $with_product_type);
    }

    /**
     * 获取无效订单列表
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return mixed
     */
    public function getInvalidList($search_data, $offset, $limit, $get_count=false, $with_product_type=false)
    {
        $search_data['status_unset_arr'] = [ OrderConst::STATUS_BIT_DEALT_CANCELED ];

        return $this->getList($search_data, $offset, $limit, $get_count, $with_product_type);
    }

    /**
     * 获取订单列表
     * @param $search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param $with_product_type
     * @return mixed
     */
    public function getList($search_data, $offset, $limit, $get_count=false, $with_product_type=false)
    {
        if(!empty($search_data['order_status'])){
            $status_set_arr = (isset($search_data['status_set_arr']) && is_array($search_data['status_set_arr'])) ? $search_data['status_set_arr'] : array();
            $status_unset_arr = (isset($search_data['status_unset_arr']) && is_array($search_data['status_unset_arr'])) ? $search_data['status_unset_arr'] : array();

            $search_status_arr = utils\QueryBuilder::parseStatusName($search_data['order_status']);
            $search_data['status_set_arr'] = array_merge($search_status_arr['status_set_arr'], $status_set_arr);
            $search_data['status_unset_arr'] = array_merge($search_status_arr['status_unset_arr'], $status_unset_arr);
        }

        return $this->_getList($search_data, $offset, $limit, $get_count, $with_product_type);
    }

    /**
     * 获取具体订单列表
     * @param $valid_search_data
     * @param $offset
     * @param $limit
     * @param bool $get_count
     * @param bool $with_product_type
     * @return mixed
     */
    abstract protected function _getList($valid_search_data, $offset, $limit, $get_count=false, $with_product_type=false);
}