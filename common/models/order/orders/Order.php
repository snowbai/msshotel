<?php

namespace common\models\order\orders;

use yii\base\Exception;
use common\models\ar\Order as OrderAR;
use common\models\ar\OrderStatusHistory as OrderStatusHistoryAR;
use common\models\ar\OrderProductHistory as OrderProductHistoryAR;
use common\models\order\OrderConst;
use common\models\base\Errorable;
use common\models\order\orders\order_no;

/**
 * 订单模版类
 * Class Order
 * @package common\models\order\orders
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
abstract class Order extends Errorable implements IOrder//模版
{
    /**
     * 订单检索AR对象
     * @var OrderAR
     */
    protected $_order_search_ar;

    /**
     * 构造函数
     * Order constructor.
     * @param $order_search_ar
     */
    protected function __construct($order_search_ar)
    {
        $this->_order_search_ar = $order_search_ar;
    }

    /**
     * 创建订单
     * @param $group_id
     * @param $hotel_id
     * @param $order_data
     * @param $products_data
     * @param $algorithm
     * @return null|object, 返回具体类型的订单对象
     * @throws \yii\db\Exception
     */
    public static function createNewOrder($group_id, $hotel_id, $order_data, $products_data=null, $algorithm=null)
    {
        if(!isset($order_data['member_id']) && !isset($order_data['apply_phone'])){
            return null; //必须设置会员ID或手机号，否则用户将无法找到该订单
        }

        $order_no_generator = new order_no\OrderNoGenerator($algorithm);
        $gen_data['group_id'] = $group_id;
        $gen_data['hotel_id'] = $hotel_id;

        $order_search_ar = new OrderAR();
        $order_search_ar->setAttributes($order_data);
        $order_search_ar->g_id = $group_id;
        $order_search_ar->h_id = $hotel_id;

        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try
        {
            //创建拥有唯一订单号的订单（在order表创建）
            $try_cnt = 0;
            do{
                $order_search_ar->order_no = $order_no_generator->generateNo($gen_data);
                $success = $order_search_ar->save();
                $try_cnt++;
                if(!$success && $try_cnt > 20){
                    throw new Exception('创建唯一订单号失败 ['.$order_search_ar->order_no.']: '.json_encode($order_search_ar->getErrors()), 1000);
                }
            }while(!$success);

            //创建特定类型的订单，并更新订单检索表（在特定的类型的订单表中创建）
            $order_obj = static::createInstance($order_search_ar, $group_id, $hotel_id, $order_data, $products_data);
            if( empty($order_obj)){
                throw new Exception('创建特定类型的订单失败：见上一条错误信息', -1);
            }
            $order_obj->updateApplyPrice();
            $transaction->commit();
            return $order_obj;
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            $errcode = $e->getCode();
            if($errcode > 0){
                self::_pushStaticError(10, $errcode, '创建订单失败', $e->getMessage());
            }
            return null;
        }
    }

    /**
     * 获取订单修改日志
     * @return string
     */
    public function getOrderHistory()
    {
        $status_history = $this->getOrderHistory();
        $products_history = $this->getOrderProductsHistory();

        return $this->_getOrderStatusDiffNote($status_history, $products_history);
    }

    /**
     * 获取订单状态历史信息
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public function getOrderStatusHistory()
    {
        $history_arr = OrderStatusHistoryAR::find()->where(['order_no'=>$this->_order_search_ar->order_no])->asArray()->all();
        return $history_arr;
    }

    /**
     * 获取购买产品变更信息
     * @param $product_type
     * @param $product_subtype
     * @param $product_id
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public function getOrderProductsHistory($product_type=null, $product_subtype=null, $product_id=null)
    {
        $query = OrderProductHistoryAR::find()->where(['order_no'=>$this->_order_search_ar->order_no]);
        if($product_type!==null) $query->andWhere(['product_type'=>$product_type]);
        if($product_subtype!==null) $query->andWhere(['product_type'=>$product_subtype]);
        if($product_id!==null) $query->andWhere(['product_type'=>$product_id]);
        $history_arr = $query->asArray()->all();

        return $history_arr;
    }

    /**
     * 更新订单信息
     * @param $order_data
     * @param $action_user_id
     * @param $action_user_name
     * @param $action_note
     * @param bool $notified
     * @return bool
     */
    public function updateOrderInfo($order_data, $action_user_id, $action_user_name, $action_note, $notified=false)
    {
        if($this->_updateOrder($order_data)){
            //暂不对修改进行记录
            return true;
        }else{
            return false;
        }
    }

    /**
     * 更新已购买产品信息
     * @param $product_id
     * @param $data
     * @param $action_user_id
     * @param $action_user_name
     * @param $action_note
     * @param bool $notified
     * @return bool
     * @throws \yii\db\Exception
     */
    public function updateProduct($product_id, $data, $action_user_id, $action_user_name, $action_note, $notified=false)
    {
        $old_product_info = $this->getProductInfo($product_id);
        $db = OrderAR::getDb();
        $transaction = $db->beginTransaction();
        try
        {
            if(!$this->_updateProduct($product_id, $data)){
                throw new Exception('更新购买产品信息失败', -1);
            }
            if(!$this->updateApplyPrice(null,false)){
                throw new Exception('更新购买产品价格失败', -1);
            }

            $transaction->commit();
            $new_product_info = $this->getProductInfo($product_id);
            $this->_logProductHistory($product_id, $old_product_info, $new_product_info,$action_user_id, $action_user_name, $action_note, $notified);
            return true;
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * 更新订单总价
     * @param $new_price
     * @param $use_transaction
     * @return bool
     * @throws \yii\db\Exception
     */
    public function updateApplyPrice($new_price=null, $use_transaction=true)
    {
        if($use_transaction){
            $db = OrderAR::getDb();
            $transaction = $db->beginTransaction();
            try
            {
                if(!$this->_updateApplyPrice($new_price)){
                    throw new Exception('更新购买产品价格失败',-1);
                }

                $this->_order_search_ar->apply_price = $this->_getApplyPrice();
                if(!$this->_order_search_ar->save()){
                    throw new Exception('更新订单检索表价格失败', 10);
                }

                $transaction->commit();
                return true;
            }
            catch(Exception $e)
            {
                $transaction->rollBack();
                $errcode = $e->getCode();
                if($errcode > 0) {
                    $this->_pushError(10, 1000, '更新订单检索表价格失败', $this->_order_search_ar->getErrors());
                }
                return false;
            }
        }else{
            if($this->_updateApplyPrice($new_price)){
                $this->_order_search_ar->apply_price = $this->_getApplyPrice();
                if($this->_order_search_ar->save()){
                    return true;
                }else{
                    $this->_error = $this->_order_search_ar->getErrors();
                    return false;
                }
            }else{
                return false;
            }
        }
    }

    /**
     * 确认订单
     * @param $action_user_id
     * @param $action_user_name
     * @param string $action_note
     * @param bool $notified
     * @return bool
     */
    public function confirm($action_user_id,$action_user_name,$action_note='',$notified=false)
    {
        return $this->setStatus(OrderConst::STATUS_BIT_CONFIRMED, true, $action_user_id, $action_user_name, $action_note, $notified);
    }

    /**
     * 设置短信已发送
     * @param $action_user_id
     * @param $action_user_name
     * @param string $action_note
     * @param bool $notified
     * @return bool
     */
    public function setSmsSent($action_user_id,$action_user_name,$action_note='',$notified=false)
    {
        return $this->setStatus(OrderConst::STATUS_BIT_SMS_NOTIFIED, true, $action_user_id, $action_user_name, $action_note, $notified);
    }

    /**
     * 设置到店支付（货到付款）
     * @param $action_user_id
     * @param $action_user_name
     * @param string $action_note
     * @param bool $notified
     * @return bool
     */
    public function setPayOnDelivery($action_user_id,$action_user_name,$action_note='',$notified=false)
    {
        return $this->setStatus(OrderConst::STATUS_BIT_PAY_ON_DELIVERY, true, $action_user_id, $action_user_name, $action_note, $notified);
    }

    /**
     * 申请退款
     * @param $action_user_id
     * @param $action_user_name
     * @param string $action_note
     * @param bool $notified
     * @return bool
     */
    public function requestRefund($action_user_id,$action_user_name,$action_note='',$notified=false)
    {
        return $this->setStatus(OrderConst::STATUS_BIT_PAY_REFUND_REQUESTED, true, $action_user_id, $action_user_name, $action_note, $notified);
    }

    /**
     * 确认退款
     * @param $action_user_id
     * @param $action_user_name
     * @param string $action_note
     * @param bool $notified
     * @return bool
     */
    public function confirmRefund($action_user_id,$action_user_name,$action_note='',$notified=false)
    {
        return $this->setStatus(OrderConst::STATUS_BIT_PAY_REFUND_CONFIRMED, true, $action_user_id, $action_user_name, $action_note, $notified);
    }

    /**
     * 设置订单状态为已支付
     * @param $pay_type
     * @param $pay_id
     * @param $action_note
     * @param bool $notified
     * @return bool
     */
    public function setPaid($pay_type, $pay_id, $action_note='', $notified=false)
    {
        $db = OrderAR::getDb();
        $transaction = $db->beginTransaction();
        try
        {
            if(!$this->_setPaid($pay_id, $pay_type)){
                throw new Exception('设置支付信息失败', -1);
            }
            if(!$this->setStatus(OrderConst::STATUS_BIT_PAY_PAID, true, 0, '支付系统', $action_note, $notified)){
                throw new Exception('设置支付状态失败', -1);
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
     * 设置状态为退款成功
     * @param $action_user_id
     * @param $action_user_name
     * @param string $action_note
     * @param bool $notified
     * @return bool
     */
    public function setRefunded($action_user_id, $action_user_name, $action_note='',$notified=false)
    {
        return $this->setStatus(OrderConst::STATUS_BIT_PAY_REFUND_SUCCESS, true, $action_user_id, $action_user_name, $action_note, $notified);
    }

    /**
     * 设置订单状态为成交状态
     * @param $action_user_id
     * @param $action_user_name
     * @param string $action_note
     * @param bool $notified
     * @return bool
     */
    public function setDealt($action_user_id,$action_user_name,$action_note='',$notified=false)
    {
        return $this->setStatus(OrderConst::STATUS_BIT_DEALT_SUCCESS, true, $action_user_id, $action_user_name, $action_note, $notified);
    }

    /**
     * 设置状态为取消
     * @param $action_user_id
     * @param $action_user_name
     * @param string $action_note
     * @param bool $notified
     * @return bool
     */
    public function cancel($action_user_id, $action_user_name, $action_note='',$notified=false)
    {
        return $this->setStatus(OrderConst::STATUS_BIT_DEALT_CANCELED, true, $action_user_id, $action_user_name, $action_note, $notified);
    }

    /**
     * 设置订单状态
     * @param $status_bit
     * @param $is_set
     * @param $action_user_id
     * @param $action_user_name
     * @param $action_note
     * @param bool $notified
     * @param bool $force
     * @return bool
     * @throws \yii\db\Exception
     */
    public function setStatus($status_bit, $is_set, $action_user_id, $action_user_name, $action_note, $notified=false, $force=false)
    {
        //计算新订单状态值
        $old_status = $this->_getStatus();
        if($is_set){
            $new_status = $status_bit | $old_status;
        }else{
            $new_status = ~$status_bit & $old_status;
        }

        //判断订单状态变更是否合法
        if(!$force && !static::_statusMachine($status_bit, $is_set, $old_status)){
            $this->_pushError(0, 5000, '订单状态更改不允许['.$old_status.']=>['.$new_status.']', '');
            return false;
        }

        //保存新订单状态
        $this->_order_search_ar->status = $new_status;
        if(!$this->_order_search_ar->save()){
            $this->_pushError(10, 1000, '订单状态更改失败', $this->_order_search_ar->getErrors());
            return false;
        }
        if(!$this->_setStatus($new_status)){
            //设回原状态
            $this->_order_search_ar->status = $old_status;
            $this->_order_search_ar->save();
            return false;
        }

        //对状态进行记录，并不保证能log成功，若log失败本次错误将会被log到文件
        $this->_logStatusHistory($status_bit, $is_set, $old_status, $action_user_id, $action_user_name, $action_note, $notified);
        return true;
    }

    /**
     * 获取具体类型订单的状态
     * @return mixed
     */
    abstract protected function _getStatus();

    /**
     * 获取订单总价
     * @return mixed
     */
    abstract protected function _getApplyPrice();

    /**
     * 更新订单信息
     * @param $data
     * @return mixed
     */
    abstract protected function _updateOrder($data);

    /**
     * 更新已购买产品信息
     * @param $product_id
     * @param $data
     * @return mixed
     */
    abstract protected function _updateProduct($product_id, $data);

    /**
     * 更新订单总价
     * @param $new_price
     * @return mixed
     */
    abstract protected function _updateApplyPrice($new_price);

    /**
     * 设置具体类型订单的状态
     * @param $new_status
     * @return mixed
     */
    abstract protected function _setStatus($new_status);

    /**
     * 设置订单支付信息
     * @param $pay_id
     * @param $pay_type
     * @return mixed
     */
    abstract protected function _setPaid($pay_id,$pay_type);

    /**
     * 状态机
     * 用于验证状态切换是否合法
     * @param $status_bit
     * @param $is_set
     * @param $old_status
     * @return bool
     */
    protected static function _statusMachine($status_bit, $is_set, $old_status)
    {
        switch($status_bit){
            case OrderConst::STATUS_BIT_PAY_PAID:
                if(!$is_set && ($old_status & $status_bit !=0)) return false; //不允许已支付订单状态更改为未支付
                else return true;
                break;
            case OrderConst::STATUS_BIT_PAY_REFUND_SUCCESS:
                if(!$is_set && ($old_status & $status_bit !=0)) return false; //不允许退款成功订单状态更改为未成功
                else return true;
                break;
            case OrderConst::STATUS_BIT_DELIVERY_SENT:
                if(!$is_set && ($old_status & $status_bit !=0)) return false; //不允许已发货订单状态更改为未发货状态
                else return true;
                break;
            case OrderConst::STATUS_BIT_DELIVERY_RECEIVED:
                if(!$is_set && ($old_status & $status_bit !=0)) return false; //不允许已收货订单状态更改为未收货
                else return true;
                break;
            case OrderConst::STATUS_BIT_DELIVERY_RETURN_SUCCESS:
                if(!$is_set && ($old_status & $status_bit !=0)) return false; //不允许已退货成功订单状态更改为未退货成功
                else return true;
                break;
            case OrderConst::STATUS_BIT_DEALT_SUCCESS:
                if(!$is_set && ($old_status & $status_bit !=0)) return false; //不允许已成交订单状态更改为未成交
                else return true;
                break;
            default:
                return true;
        }
    }

    /**
     * 记录订单状态改变历史
     * @param $status_bit
     * @param $is_set
     * @param $old_status
     * @param $action_user_id
     * @param $action_user_name
     * @param $action_note
     * @param $notified
     * @return bool
     */
    protected function _logStatusHistory($status_bit, $is_set, $old_status, $action_user_id, $action_user_name, $action_note, $notified)
    {
        $history_ar = new OrderStatusHistoryAR();
        $order_id = $this->_order_search_ar->order_id;
        $order_no = $this->_order_search_ar->order_no;
        $diff_note = $this->_getOrderStatusDiffNote($status_bit,$is_set);

        $history_ar->order_id = $order_id;
        $history_ar->order_no = $order_no;
        $history_ar->order_status = $old_status;
        $history_ar->diff = $status_bit;
        $history_ar->note = $diff_note;
        $history_ar->action_user_id = $action_user_id;
        $history_ar->action_user_name = $action_user_name;
        $history_ar->action_note = $action_note;
        $history_ar->customer_notified = intval($notified);

        if($history_ar->save()){
            return true;
        }else{
            $this->_pushError(1, 1000, '记录订单状态历史失败', $history_ar->getErrors());
            return false;
        }
    }

    /**
     * 记录购买产品的修改历史
     * @param $product_id
     * @param $new_products_info
     * @param $old_products_info
     * @param $action_user_id
     * @param $action_user_name
     * @param $action_note
     * @param $notified
     * @return bool
     */
    protected function _logProductHistory($product_id, $new_products_info, $old_products_info, $action_user_id, $action_user_name, $action_note, $notified)
    {
        $history_ar = new OrderProductHistoryAR();
        $order_id = $this->_order_search_ar->order_id;
        $order_no = $this->_order_search_ar->order_no;
        $product_info = $this->getProductInfo($product_id);
        $diff = $this->_getOrderProductDiff($new_products_info,$old_products_info);
        $diff_note = $this->_getOrderProductDiffNote($new_products_info, $old_products_info);

        $history_ar->order_id = $order_id;
        $history_ar->order_no = $order_no;
        $history_ar->product_type = $product_info['product_type'];
        $history_ar->product_subtype = $product_info['product_subtype'];
        $history_ar->product_id = $product_id;
        $history_ar->snapshop = json_encode($old_products_info);
        $history_ar->diff = $diff;
        $history_ar->note = $diff_note;
        $history_ar->action_user_id = $action_user_id;
        $history_ar->action_user_name = $action_user_name;
        $history_ar->action_note = $action_note;
        $history_ar->customer_notified = intval($notified);

        if($history_ar->save()){
            return true;
        }else{
            $this->_pushError(1, 1000, '记录订单产品历史失败', $history_ar->getErrors());
            return false;
        }
    }

    /**
     * 生成订单历史说明
     * @param $status_history
     * @param $product_history
     * @return array
     */
    protected function _parseOrderHistoryNote($status_history, $product_history)
    {
        $operation_log = array();

        foreach($status_history as $operation){
            $operation_log[$operation['add_time']]['operator'] = $operation['action_user_name'];
            $operation_log[$operation['add_time']]['note'] = $operation['note'];
            $operation_log[$operation['add_time']]['time'] = $operation['add_time'];
        }

        foreach($product_history as $operation){
            $operation_log[$operation['add_time']]['operator'] = $operation['action_user_name'];
            $operation_log[$operation['add_time']]['note'] = $operation['note'];
            $operation_log[$operation['add_time']]['time'] = $operation['add_time'];
        }

        krsort($operation_log);
        return $operation_log;
    }

    /**
     * 获取产品修改差异信息
     * @param $new_product_info
     * @param $old_product_info
     * @return mixed
     */
    abstract protected function _getOrderProductDiff($new_product_info, $old_product_info);

    /**
     * 获取订单状态差异说明
     * @param $status_bit
     * @param $is_set
     * @return string
     */
    protected static function _getOrderStatusDiffNote($status_bit, $is_set)
    {
        switch($status_bit){
            case OrderConst::STATUS_BIT_CONFIRMED:
                $diff = $is_set ? '酒店确认订单' : '确认订单状态取消';
                break;
            case OrderConst::STATUS_BIT_SMS_NOTIFIED:
                $diff = $is_set ? '预定成功短信已发送' : '短信发送成功状态取消';
                break;
            case OrderConst::STATUS_BIT_PAY_ON_DELIVERY:
                $diff = $is_set ? '修改为到店支付' : '修改为立即支付';
                break;
            case OrderConst::STATUS_BIT_PAY_PAID:
                $diff = $is_set ? '付款成功' : '付款成功状态取消';
                break;
            case OrderConst::STATUS_BIT_PAY_REFUND_REQUESTED:
                $diff = $is_set ? '申请退款' : '申请退款状态取消';
                break;
            case OrderConst::STATUS_BIT_PAY_REFUND_CONFIRMED:
                $diff = $is_set ? '酒店确认退款' : '退款已确认状态取消';
                break;
            case OrderConst::STATUS_BIT_PAY_REFUND_DECLINED:
                $diff = $is_set ? '退款申请未通过' : '退款申请未通过状态取消';
                break;
            case OrderConst::STATUS_BIT_PAY_REFUND_SUCCESS:
                $diff = $is_set ? '退款成功' : '退款成功状态取消';
                break;
            case OrderConst::STATUS_BIT_DELIVERY_SENT:
                $diff = $is_set ? '已发货' : '已发货状态取消';
                break;
            case OrderConst::STATUS_BIT_DELIVERY_RECEIVED:
                $diff = $is_set ? '已收货' : '已收货状态取消';
                break;
            case OrderConst::STATUS_BIT_DELIVERY_RETURN_REQUESTED:
                $diff = $is_set ? '申请退货' : '申请退货取消';
                break;
            case OrderConst::STATUS_BIT_DELIVERY_RETURN_DECLINED:
                $diff = $is_set ? '申请退货' : '申请退货取消';
                break;
            case OrderConst::STATUS_BIT_DELIVERY_RETURN_CONFIRMED:
                $diff = $is_set ? '退货申请未通过' : '退货申请未通过状态取消';
                break;
            case OrderConst::STATUS_BIT_DELIVERY_RETURN_SUCCESS:
                $diff = $is_set ? '退货成功' : '退货成功状态取消';
                break;
            case OrderConst::STATUS_BIT_DEALT_SUCCESS:
                $diff = $is_set ? '交易完成' : '交易完成成功状态取消';
                break;
            case OrderConst::STATUS_BIT_DEALT_CANCELED:
                $diff = $is_set ? '交易取消' : '交易取消状态取消';
                break;
            default:
                $diff = '未知状态（状态位:'.$status_bit.';状态设置:'.$is_set.'）';
        }

        return $diff;
    }

    /**
     * 获取产品修改差异说明
     * @param $new_product_info
     * @param $old_product_info
     * @return mixed
     */
    abstract protected function _getOrderProductDiffNote($new_product_info, $old_product_info);
}
