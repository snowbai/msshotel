<?php

namespace common\models\order\products;
use common\models\order\OrderConst;

/**
 * 产品工厂类
 * Class ProductFactory
 * @package common\models\order\products
 */
class ProductFactory //工厂
{
    /**
     * 动态创建产品对象
     * @param $product_type
     * @param $product_subtype
     * @param $product_id
     * @param null $product_attr
     * @return IOrderable|null
     */
    public static function getInstance($product_type, $product_subtype, $product_id, $product_attr=null)
    {
        switch($product_type){
            case OrderConst::ORDER_ROOM:
                $product_obj = self::_getRoomInstance($product_subtype, $product_id, $product_attr);
                break;
            case OrderConst::ORDER_PROMOTION:
                $product_obj = self::_getPromotionInstance($product_subtype, $product_id, $product_attr);
                break;
            case OrderConst::ORDER_DINNER:
                $product_obj = self::_getDinnerInstance($product_subtype, $product_id, $product_attr);
                break;
            default:
                $product_obj = null;
        }
        return $product_obj;
    }

    /**
     * 动态获取某产品类型的信息
     * @param $product_type
     * @param $product_subtype
     * @return array|null
     */
    public static function getTypeInfo($product_type, $product_subtype)
    {
        switch($product_type){
            case 1:
                $version_info = Room::getTypeInfo();
                break;
            case 2:
                $version_info = PromOrdinary::getTypeInfo();
                break;
            case 3:
                $version_info = Dinner::getTypeInfo();
                break;
            default:
                $version_info = null;
        }
        return $version_info;
    }

    /**
     * 获取客房产品对戏那个
     * @param $subtype
     * @param $product_id
     * @param $product_attr
     * @return Room|null
     */
    protected function _getRoomInstance($subtype, $product_id, $product_attr)
    {
        switch($subtype){
            case 0:
                $product_obj = Room::getInstance($product_id, $product_attr);
                break;
            default:
                $product_obj = RoomTemai::getInstance($product_id, $product_attr);
        }

        return $product_obj;
    }

    /**
     * 获取活动产品对象
     * @param $subtype
     * @param $product_id
     * @param $product_attr
     * @return PromOrdinary|null
     */
    protected function _getPromotionInstance($subtype, $product_id, $product_attr)
    {
        switch($subtype){
            case 0:
                $product_obj = PromOrdinary::getInstance($product_id, $product_attr);
                break;
            default:
                $product_obj = null;
        }

        return $product_obj;
    }

    /**
     * 获取餐饮产品对象
     * @param $subtype
     * @param $product_id
     * @param $product_attr
     * @return PromOrdinary|null
     */
    protected function _getDinnerInstance($subtype, $product_id, $product_attr)
    {
        switch($subtype){
            case 0:
                $product_obj = Dinner::getInstance($product_id, $product_attr);
                break;
            default:
                $product_obj = null;
        }

        return $product_obj;
    }
}