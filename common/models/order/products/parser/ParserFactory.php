<?php

namespace common\models\order\products\parser;
use common\models\order\OrderConst;

/**
 * 数据处理工厂类
 * Class ParserFactory
 * @package common\models\order\products\parser
 */
class ParserFactory
{
    /**
     * 获取指定产品类型的数据处理类
     * @param $product_type
     * @param $product_subtype
     * @return RoomParser|null
     */
    public static function getParser($product_type, $product_subtype)
    {
        switch($product_type){
            case OrderConst::ORDER_ROOM:
                if($product_subtype == 0){
                    return new RoomParser();
                }elseif($product_subtype == 1){
                    return new RoomTemaiParser();
                }else{
                    return null;
                }
                break;
            case OrderConst::ORDER_PROMOTION:
                if($product_subtype == 0){
                    return new PromOrdinaryParser();
                }else{
                    return null;
                }
                break;
            case OrderConst::ORDER_DINNER:
                if($product_subtype == 0){
                    return new DinnerParser();
                }else{
                    return null;
                }
                break;
            default:
                return null;
        }
    }
}
