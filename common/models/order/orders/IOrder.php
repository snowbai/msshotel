<?php

namespace common\models\order\orders;

/**
 * 订单接口类
 * Interface IOrder
 * @package common\models\order\orders
 */
interface IOrder
{
    /*
     * 创建或获取订单
     */
    public static function getInstance($order_no, $order_id=0, $order_ar=null, $order_search_ar=null);
    public static function createNewOrder($group_id, $hotel_id, $order_data, $products_data=null, $algorithm=null);
    public static function createInstance($order_search_ar, $group_id, $hotel_id, $order_data, $products_data);

    /*
     * 获取订单信息
     */
    public function getOrderNo();
    public function getOrderStatus();
    public function getOrderInfo();
    public static function getProductType();
    public function getProductInfo($product_id);
    public function getProductsInfo();
    public function getOrderHistory();
    public function getOrderStatusHistory();
    public function getOrderProductsHistory($product_type=null, $product_subtype=null, $product_id=null);

    /*
     * 更新订单信息
     */
    public function updateOrderInfo($order_data, $action_user_id, $action_user_name, $action_note, $notified=false);
    public function updateProduct($product_id, $data, $action_user_id, $action_user_name, $action_note, $notified=false);
    public function updateApplyPrice($new_price=null, $use_transaction=true);
    public function addNote($note);

    /*
     * 更新订单状态
     */
    public function confirm($action_user_id,$action_user_name,$action_note='',$notified=false);
    public function setSmsSent($action_user_id,$action_user_name,$action_note='',$notified=false);
    public function setPayOnDelivery($action_user_id,$action_user_name,$action_note='',$notified=false);
    public function requestRefund($action_user_id,$action_user_name,$action_note='',$notified=false);
    public function confirmRefund($action_user_id,$action_user_name,$action_note='',$notified=false);
    public function setPaid($pay_type, $pay_id, $action_note='', $notified=false);
    public function setRefunded($action_user_id, $action_user_name, $action_note='',$notified=false);
    public function setDealt($action_user_id,$action_user_name,$action_note='',$notified=false);
    public function cancel($action_user_id, $action_user_name, $action_note='',$notified=false);
    public function setStatus($status_bit, $is_set, $action_user_id, $action_user_name, $action_note, $notified=false, $force=false);

}