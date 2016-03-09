<?php

namespace common\models\order;

use common\models\base\Errorable;
use common\models\order\orders;
use common\models\order\products;
use common\models\order\products\parser\ComputedData;
use common\models\order\products\parser\ParserFactory;
use yii\base\Exception;

/**
 * 订单创建类
 * Class OrderCreator
 * @package common\models\order
 */
class OrderCreator extends Errorable
{
    /**
     * 产品类型
     * @var int
     */
    protected $_product_type;

    /**
     * 产品子类型
     * @var int
     */
    protected $_product_subtype;

    /**
     * 数据解析处理对象
     * @var products\parser\RoomParser|null
     */
    protected $_parser;

    /**
     * 创建信息
     * @var
     */
    protected $_create_info;

    /**
     * 构造函数
     * OrderCreator constructor.
     * @param $product_type
     * @param $product_subtype
     */
    public function __construct($product_type, $product_subtype)
    {
        $this->_product_type = (int) $product_type;
        $this->_product_subtype = (int) $product_subtype;
        $this->_parser = ParserFactory::getParser($this->_product_type, $this->_product_subtype);
    }

    /**
     * 创建订单
     * @param $group_id
     * @param $hotel_id
     * @param $order_data
     * @param $products_data
     * @param $attr
     * @return null|object
     */
    public function createOrder($group_id, $hotel_id, $order_data, $products_data, $attr)
    {
        if(empty($this->_parser)){
            $this->_pushError(0, 5000, '产品类型不合法', null);
            return null;
        }

        $ordered_products = $this->_parser->getProductsList($order_data, $products_data, $attr);
        $result = $this->_doDecAndCalc($ordered_products);
        if($result === false) return null;

        $composed_order_data = $this->_parser->composeOrderData($order_data, $result);
        $composed_products_data = $this->_parser->composeProductsData($products_data, $result);

        $order_obj = $this->_doCreateNewOrder($group_id, $hotel_id, $composed_order_data, $composed_products_data);

        if(empty($order_obj)){ //创建失败恢复库存
            $this->_doIncNum($ordered_products);
        }else{
            $this->_create_info['inventory_info'] = $result->inventory_info;
        }

        return $order_obj;

    }

    /**
     * 减少库存并计算价格
     * @param $ordered_products
     * @return bool
     * @throws \yii\db\Exception
     */
    public function _doDecAndCalc($ordered_products)
    {
        $total_apply_price = 0;
        $products_info = array();
        $inventory_info = array();

        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try{
            foreach($ordered_products as $ordered_product)
            {
                $product_id = $ordered_product['product_id'];
                $apply_num = $ordered_product['apply_num'];
                $discount_data = $ordered_product['discount_data'];
                $apply_attr = $ordered_product['apply_attr'];
                $product_obj = products\ProductFactory::getInstance($this->_product_type, $this->_product_subtype, $product_id, $apply_attr);
                if(empty($product_obj)) throw new Exception('产品不存在');

                $products_info[$product_id] = $product_obj->getInfo();
                $inventory_info[$product_id]['before'] = $product_obj->getRemainNum();
                if($product_obj->decRemainNum($apply_num, false)){
                    $inventory_info[$product_id]['after'] = $product_obj->getRemainNum();
                    $apply_price = $product_obj->calcApplyPrice($apply_num, $discount_data);
                    $total_apply_price += $apply_price;
                }else{
                    throw new Exception('产品剩余数量不足');
                }
            }
            $transaction->commit();
        }catch(Exception $e){
            $transaction->rollBack();
            $this->_errors[] = ['code'=>1100, 'msg'=>$e->getMessage(), 'details'=>''];
            return false;
        }

        $compute_data = new ComputedData();
        $compute_data->apply_price = $total_apply_price;
        $compute_data->products_info = $products_info;
        $compute_data->inventory_info = $inventory_info;

        return $compute_data;
    }

    /**
     * 增加库存
     * @param $ordered_products
     */
    protected function _doIncNum($ordered_products)
    {
        foreach($ordered_products as $ordered_product)
        {
            $product_id = $ordered_product['product_id'];
            $apply_num = $ordered_product['apply_num'];
            $apply_attr = $ordered_product['apply_attr'];

            $product_obj = products\ProductFactory::getInstance($this->_product_type, $this->_product_subtype, $product_id, $apply_attr);
            if(!$product_obj->decRemainNum($apply_num, true)){
                $this->_pushError(5, 1000, '保存订单失败', 'product_subtype不合法');
                $this->_errors[] = ['code'=>1000, 'msg'=>'恢复库存失败', 'details'=>$product_obj->getErrors()];
            }
        }
    }

    /**
     * 创建特定类型的订单
     * @param $group_id
     * @param $hotel_id
     * @param $order_data
     * @param $products_data
     * @return null
     */
    public function _doCreateNewOrder($group_id, $hotel_id, $order_data, $products_data)
    {
        switch($this->_product_type){
            case OrderConst::ORDER_ROOM:
                if(in_array($this->_product_subtype,[0,1])){
                    return orders\RoomOrder::createNewOrder($group_id, $hotel_id, $order_data, $products_data);
                }else{
                    $this->_pushError(0, 5000, '保存订单失败', 'product_subtype不合法');
                    return null;
                }
            case OrderConst::ORDER_PROMOTION:
                if($this->_product_subtype==0){
                    return orders\promotionOrder::createNewOrder($group_id, $hotel_id, $order_data, $products_data);
                }else{
                    $this->_pushError(0, 5000, '保存订单失败', 'product_subtype不合法');
                    return null;
                }
            case OrderConst::ORDER_DINNER:
                if($this->_product_subtype==0){
                    return orders\DinnerOrder::createNewOrder($group_id, $hotel_id, $order_data, $products_data);
                }else{
                    $this->_pushError(0, 5000, '保存订单失败', 'product_subtype不合法');
                    return null;
                }
            default:
                $this->_pushError(0, 5000, '保存订单失败', 'product_type不合法');
                return null;
        }
    }
}