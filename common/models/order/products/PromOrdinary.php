<?php

namespace common\models\order\products;

use common\models\base\Constant;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivityOrdinary as OrdinaryAR;
use yii\base\Exception;

/**
 * 普通活动产品类
 * Class PromOrdinary
 * @package common\models\order\products
 */
class PromOrdinary extends OrderableBase implements IOrderable
{
    /**
     * 普通活动价格对象
     * @var OrdinaryAR
     */
    protected $_ordinary_ar;

    /**
     * 构造函数
     * PromOrdinay constructor.
     * @param $product_id
     * @param $attr
     * @param $ordinary_ar
     */
    protected function __construct($product_id, $attr, $ordinary_ar)
    {
        parent::__construct($product_id, $attr);
        $this->_ordinary_ar = $ordinary_ar;
    }

    /**
     * 获取对象
     * @param $product_id
     * @param $attr
     * @return Room|null
     */
    public static function getInstance($product_id, $attr)
    {
        $ordinary_ar = OrdinaryAR::findOne(['prom_id'=>$product_id]);
        if(empty($ordinary_ar)) return null;
        return new PromOrdinary($product_id, $attr, $ordinary_ar);
    }

    /**
     * 获取产品版本信息
     * @param $attr
     * @return array
     */
    public static function getTypeInfo($attr=null)
    {
        return ['type_name'=>'promotion'];
    }

    /**
     * 获取产品信息
     * @param $attr
     * @return null
     */
    public function getInfo($attr=null)
    {
        if(empty($this->_ordinary_ar)) return null;
        $prom_ar = PromotionAR::findOne($this->_product_id);
        if(empty($prom_ar)) return null;
        $info['prom_info'] = $prom_ar->toArray();
        $info['price_info'] = $this->_ordinary_ar->toArray();

        return $info;
    }

    /**
     * 获取产品预留数量
     * @return int
     */
    public function getReserveNum()
    {
        if(empty($this->_ordinary_ar)) {
            return 0;
        }

        return $this->_ordinary_ar->num;
    }

    /**
     * 获取产品剩余数量
     * @return int
     */
    public function getRemainNum()
    {
        if(empty($this->_ordinary_ar)) {
            return 0;
        }

        return $this->_ordinary_ar->remain_num;
    }

    /**
     * 获取价格
     * @param $apply_num
     * @param $discount_data
     * @return bool|mixed
     */
    public function calcApplyPrice($apply_num, $discount_data)
    {
        if(empty($this->_ordinary_ar)) return false;

        $total_price = $this->_ordinary_ar->price * $apply_num;
        $apply_price =  $this->_calculateDiscount($total_price, $discount_data, $this->_attr);

        return $apply_price;
    }

    /**
     * 调整预留数量
     * @param $new_amount
     * @return bool
     */
    public function adjustReserveNum($new_amount)
    {
        if(empty($this->_ordinary_ar)) return false;

        try{
            $oldNum = $this->_ordinary_ar->num;
            $ups = intval($new_amount) - $oldNum;
            $this->_ordinary_ar->num = $new_amount;
            if($this->_ordinary_ar->save() && $this->_ordinary_ar->updateCounters(['remain_num'=>$ups])){
                return true;
            }else{
                throw new Exception('调整剩余数量失败');
            }
        }catch(Exception $e){
            $this->_pushError(0, 5000, $e->getMessage(), $this->_ordinary_ar->getErrors());
            return false;
        }
    }

    /**
     * 增加剩余数量
     * @param $num
     * @param $force
     * @return false|array
     */
    public function incRemainNum($num, $force=true)
    {
        if(empty($this->_ordinary_ar)){
            $this->_pushError(0, 5000, '活动不存在', null);
            return false;
        }
        if(!$force){
            $prom_ar = PromotionAR::findOne($this->_product_id);
            if(empty($prom_ar) || in_array($prom_ar->status,[Constant::STATUS_DELETED, Constant::STATUS_BLOCKED])){
                $this->_pushError(0, 5000, '活动已关闭或删除', null);
                return false;
            }
        }

        try{
            $this->_ordinary_ar->remain_num;
            if(!$this->_ordinary_ar->updateCounters(['remain_num'=>$num])){
                throw new Exception('调整剩余数量失败');
            }
            return true;
        }catch(Exception $e){
            $this->_pushError(0, 5000, $e->getMessage(), $this->_ordinary_ar->getErrors());
            return false;
        }
    }

    /**
     * 减少剩余数量
     * @param $num
     * @param bool $force
     * @return array|false
     */
    public function decRemainNum($num, $force = false)
    {
        return $this->incRemainNum(-$num, $force);
    }
}