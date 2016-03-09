<?php

namespace common\models\order;

use common\models\base\Errorable;
use yii\base\Exception;
use common\models\ar\Order as OrderSearchAR;
use common\models\order\orders\OrderFactory;
use common\models\order\products\parser\ParserFactory;
use common\models\order\products\ProductFactory;

/**
 * 订单修改类
 * Class OrderModifier
 * @package common\models\order
 */
class OrderModifier extends Errorable
{
    /**
     * 订单对象
     * @var object
     */
    protected $_order_obj;

    /**
     * 构造函数
     * OrderModifier constructor.
     * @param $order_obj
     */
    protected function __construct($order_obj)
    {
        $this->_order_obj = $order_obj;
    }

    /**
     * 获取对象
     * @param $order_no
     * @param int $order_id
     * @param null $order_ar
     * @param null $order_search_ar
     * @return OrderModifier|null
     */
    public static function getInstance($order_no, $order_id=0, $order_ar=null, $order_search_ar=null)
    {
        $order_obj = OrderFactory::getInstance($order_no, $order_id, $order_ar, $order_search_ar);
        if(empty($order_obj)) return null;
        return new OrderModifier($order_obj);
    }

    /**
     * 获取订单对象
     * @return orders\PromotionOrder|null|object
     */
    public function getOrderObj()
    {
        return $this->_order_obj;
    }

    /**
     * 修改订单基本信息
     * @param $new_data
     * @param string $action_user_id
     * @param string $action_user_name
     * @param string $action_note
     * @return bool
     */
    public function modifyOrderInfo($new_data, $action_user_id='0', $action_user_name='系统', $action_note='')
    {
        if(empty($this->_order_obj)) return false;
        return $this->_order_obj->updateOrderInfo($new_data, $action_user_id, $action_user_name, $action_note);
    }

    /**
     * 修改订单中购买的产品信息
     * @param $order_product_id
     * @param $new_product_apply_data
     * @param $attr
     * @param string $action_user_id
     * @param string $action_user_name
     * @param string $action_note
     * @return bool
     * @throws \yii\db\Exception
     */
    public function modifyPurchasedProduct($order_product_id, $new_product_apply_data, $attr, $action_user_id='0', $action_user_name='系统', $action_note='')
    {
        if(empty($this->_order_obj)) return false;

        $product_info = $this->_order_obj->getProductInfo($order_product_id);
        $product_type = $product_info['product_type'];
        $product_subtype = $product_info['product_subtype'];
        $product_id = $product_info['product_id'];
        $origin_apply_num = $product_info['apply_num'];

        $parser = ParserFactory::getParser($product_type, $product_subtype);
        if(empty($parser)) return false;
        $new_data = $parser->processProductData($new_product_apply_data, $attr);
        $new_apply_num = $new_data['apply_num'];
        $new_attr = $new_data['apply_attr'];
        $new_discount_data = $new_data['discount_data'];

        $product_obj = ProductFactory::getInstance($product_type, $product_subtype, $product_id, $new_attr);
        if(empty($product_obj)) return false;

        $db = OrderSearchAR::getDb();
        $transaction = $db->beginTransaction();
        try
        {
            if($new_apply_num!=$origin_apply_num){
                if(!$product_obj->decRemainNum($new_apply_num-$origin_apply_num)){
                    throw new Exception('剩余数量不足'."org:$origin_apply_num,new:$new_apply_num,product_id:$product_id"/*.$product_obj->getErrorMessage()*/);
                }

                $new_product_apply_data['apply_price'] = $product_obj->calcApplyPrice($new_apply_num, $new_discount_data);
            }

            if(!$this->_order_obj->updateProduct($order_product_id,$new_product_apply_data,$action_user_id,$action_user_name,$action_note)){
                throw new Exception('修改购买产品信息失败');
            }

            if(!$this->_order_obj->updateApplyPrice()){
                throw new Exception('更新价格失败');
            }

            $transaction->commit();
            return true;
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            $this->_pushError(0, 5000, $e->getMessage(), '');
            return false;
        }
    }
}