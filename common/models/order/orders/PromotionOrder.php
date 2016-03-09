<?php

namespace common\models\order\orders;

use common\models\ar\Order as OrderAR;
use common\models\ar\OrderPromotion as PromotionOrderAR;
use common\models\order\OrderConst;

/**
 * 活动订单业务处理逻辑
 * Class PromotionOrder
 * @package common\models\order\orders
 */
class PromotionOrder extends Order
{
    /**
     * 具体订单对象
     * @var PromotionOrderAR
     */
    protected $_order_ar;

    /**
     * 构造函数
     * Order constructor.
     * @param $order_ar
     * @param $order_search_ar
     */
    protected function __construct($order_ar, $order_search_ar)
    {
        parent::__construct($order_search_ar);
        $this->_order_ar = $order_ar;
    }

    /**
     * 获取对象
     * @param $order_no
     * @param int $order_id
     * @param null $order_ar
     * @param null $order_search_ar
     * @return PromotionOrder|null
     */
    public static function getInstance($order_no, $order_id = 0, $order_ar = null, $order_search_ar = null)
    {
        if(empty($order_search_ar)){
            if(!empty($order_id)){
                $order_search_ar = OrderAR::findOne(['id'=>(int)$order_id]);
            }elseif(!empty($order_no)){
                $order_search_ar = OrderAR::findOne(['order_no'=>(string)$order_no]);
            }
        }
        if(empty($order_search_ar)) return null;

        if(empty($order_ar)){
            $order_ar = PromotionOrderAR::findOne(['id'=>(int)$order_search_ar->order_id]);
        }
        if(empty($order_ar)) return null;

        return new PromotionOrder($order_ar, $order_search_ar);
    }

    /**
     * 创建具体订单
     * @param $order_search_ar
     * @param $group_id
     * @param $hotel_id
     * @param $order_data
     * @param $products_data
     * @return PromotionOrder|null
     */
    public static function createInstance($order_search_ar, $group_id, $hotel_id, $order_data, $products_data)
    {
        if(empty($order_search_ar)) return null;
        $order_search_ar->product_type = OrderConst::ORDER_PROMOTION;
        $order_search_ar->product_subtype = isset($order_data['prom_type']) ? $order_data['prom_type'] : 0;
        $order_search_ar->save();

        $order_ar = new PromotionOrderAR();
        $order_ar->setAttributes($order_data);
        $order_ar->order_id = $order_search_ar->order_id;
        $order_ar->order_no = $order_search_ar->order_no;
        $order_ar->g_id = $group_id;
        $order_ar->h_id = $hotel_id;

        if($order_ar->save()){
            return new PromotionOrder($order_ar,$order_search_ar);
        }else{
            self::_pushStaticError(10, 1000, '创建订单失败：保存订单失败', $order_ar->getErrors());
            return null;
        }
    }

    /**
     * 获取订单编号
     * @return string
     */
    public function getOrderNo()
    {
        return $this->_order_ar->order_no;
    }

    /**
     * 获取订单状态
     * @return int
     */
    public function getOrderStatus()
    {
        return $this->_order_ar->order_status;
    }

    /**
     * 获取订单信息
     * @return array|null
     */
    public function getOrderInfo()
    {
        return $this->_order_ar->toArray();
    }

    /**
     * 获取产品类型信息
     * @return array
     */
    public static function getProductType()
    {
        return ['type'=>OrderConst::ORDER_PROMOTION];
    }

    /**
     * 获取购买产品信息
     * @param $product_id
     * @return array|null
     */
    public function getProductInfo($product_id)
    {
        $product_info = $this->_order_ar->toArray();
        $product_info['product_type'] = OrderConst::ORDER_PROMOTION;
        $product_info['product_subtype'] = $product_info['prom_type'];
        $product_info['product_id'] = $product_info['prom_id'];

        return $product_info;
    }

    /**
     * 获取购买的所有产品信息
     * @return mixed
     */
    public function getProductsInfo()
    {
        $products_arr[0] = $this->getProductInfo(0);
        return $products_arr;
    }

    /**
     * 后台添加备注
     * @param $note
     * @return bool
     */
    public function addNote($note)
    {
        $this->_order_ar->note = $note;
        if($this->_order_ar->save()){
            return true;
        }else{
            $this->_pushError(3,3000,'添加备注信息失败',$this->_order_ar->getErrors());
            return false;
        }
    }

    /**
     * 更新订单信息（不包含购买产品信息）
     * @param $data
     * @return bool
     */
    protected function _updateOrder($data)
    {
        if(isset($data['apply_name'])) $this->_order_ar->apply_name = $data['apply_name'];
        if(isset($data['apply_phone'])) $this->_order_ar->apply_name = $data['apply_phone'];
        if(isset($data['apply_request'])) $this->_order_ar->apply_name = $data['apply_request'];

        return $this->_order_ar->save();
    }

    /**
     * 更新购买的产品信息
     * @param $product_id
     * @param $data
     * @return bool
     */
    protected function _updateProduct($product_id, $data)
    {
        unset($data['order_id']);
        unset($data['order_no']);
        unset($data['order_type']);
        unset($data['order_pid']);
        unset($data['g_id']);
        unset($data['h_id']);
        unset($data['member_id']);
        unset($data['member_grade']);
        unset($data['apply_name']);
        unset($data['apply_phone']);

        $this->_order_ar->setAttributes($data);
        if($this->_order_ar->save()){
            return true;
        }else{
            $this->_pushError(5, 2000, '更新产品信息失败', $this->_order_ar->getErrors());
            return false;
        }
    }

    /**
     * 获取购买价格
     * @return int
     */
    public function _getApplyPrice()
    {
        return $this->_order_ar->apply_price;
    }

    /**
     * 更新购买价格
     * @param $new_price
     * @return bool
     */
    public function _updateApplyPrice($new_price)
    {
        if($new_price===null) return true;

        $this->_order_ar->apply_price = $new_price;
        if($this->_order_ar->save()){
            return true;
        }else{
            $this->_pushError(5, 2000, '更新产品价格失败', $this->_order_ar->getErrors());
            return false;
        }
    }

    /**
     * 获取订单状态
     * @return bool
     */
    protected function _getStatus()
    {
        return $this->_order_ar->order_status;
    }

    /**
     * 设置订单状态
     * @param $new_status
     * @return bool
     */
    protected function _setStatus($new_status)
    {
        $this->_order_ar->order_status = $new_status;
        return $this->_order_ar->save();
    }

    /**
     * 设置支付信息
     * @param $pay_type
     * @param $pay_id
     * @return bool
     */
    protected function _setPaid($pay_type, $pay_id)
    {
        $this->_order_ar->pay_type = $pay_type;
        $this->_order_ar->pay_id = $pay_id;
        return $this->_order_ar->save();
    }

    /**
     * 获取修改订单前后的差异信息
     * @param $new_product_info
     * @param $old_product_info
     * @return null
     */
    protected function _getOrderProductDiff($new_product_info, $old_product_info)
    {
        return null;
    }

    /**
     * 获取修改购买产品的差异信息
     * @param $new_product_info
     * @param $old_product_info
     * @return string
     */
    protected function _getOrderProductDiffNote($new_product_info, $old_product_info)
    {
        $product_name = isset($new_product_info['prom_name']) ? $new_product_info['prom_name'] : $old_product_info['prom_name'];

        $diff = '';
        if( isset($new_product_info['prom_type']) &&
            ($new_product_info['prom_type'] != $old_product_info['prom_type'] || $new_product_info['prom_id'] != $old_product_info['prom_id']) )
        {
            $diff .= '购买产品'.$product_name.'修改为'.$new_product_info['prom_name'].'; ';
        }

        if( isset($new_product_info['apply_num']) &&
            $new_product_info['apply_num'] != $old_product_info['apply_num'])
        {
            $diff .= '购买'.$product_name.'数量'.$old_product_info['apply_num'].'修改为'.$new_product_info['apply_num'].'; ';
        }

        if( isset($new_product_info['apply_price']) &&
            $new_product_info['apply_price'] != $old_product_info['apply_price'])
        {
            $diff .= '购买'.$product_name.'价格'.$old_product_info['apply_price'].'修改为'.$new_product_info['apply_price'].'; ';
        }

        if( isset($new_product_info['apply_arrive_date']) &&
            $new_product_info['apply_arrive_date'] != $old_product_info['apply_arrive_date'])
        {
            $diff .= $product_name.'到店日期'.$old_product_info['apply_arrive_date'].'修改为'.$new_product_info['apply_arrive_date'].'; ';
        }

        if( isset($new_product_info['apply_leave_date']) &&
            $new_product_info['apply_leave_date'] != $old_product_info['apply_leave_date'])
        {
            $diff .= $product_name.'离店日期'.$old_product_info['apply_leave_date'].'修改为'.$new_product_info['apply_leave_date'].'; ';
        }

        return $diff;
    }
}
