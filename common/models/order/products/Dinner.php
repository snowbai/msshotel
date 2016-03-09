<?php

namespace common\models\order\products;

use yii\base\Exception;
use common\models\ar\Dinner as DinnerAR;
use common\models\ar\DinnerSet as DinnerSetAR;

/**
 * 餐饮产品类
 * Class Dinner
 * @package common\models\order\products
 */
class Dinner extends OrderableBase implements IOrderable
{
    /**
     * 客房价格记录数组
     * @var DinnerSetAR
     */
    protected $_dinner_set_ar;

    /**
     * 构造函数
     * Dinner constructor.
     * @param $product_id
     * @param $attr
     * @param $dinner_set_ar
     */
    protected function __construct($product_id, $attr, $dinner_set_ar)
    {
        parent::__construct($product_id, $attr);
        $this->_dinner_set_ar = $dinner_set_ar;
    }

    /**
     * 获取对象
     * @param $product_id
     * @param $attr
     * @return Room|null
     */
    public static function getInstance($product_id, $attr)
    {
        $arrive_date = isset($attr['apply_arrive_date']) ? strtotime($attr['apply_arrive_date']) : strtotime(date('Y-m-d'));

        $dinner_set_ar = DinnerSetAR::find()->where(['dinner_id'=>$product_id])
            ->andWhere(['=', 'date', $arrive_date])
            ->one();
        if(empty($dinner_set_ar)) return null;
        return new Dinner($product_id, $attr, $dinner_set_ar);
    }

    /**
     * 获取产品信息
     * @param $attr
     * @return array
     */
    public static function getTypeInfo($attr=null)
    {
        return ['type_name'=>'dinner'];
    }

    /**
     * 获取产品信息
     * @return null
     */
    public function getInfo()
    {
        if(empty($this->_dinner_set_ar)) return null;
        $dinner_ar = DinnerAR::findOne($this->_product_id);
        if(empty($dinner_ar)) return null;

        $info['dinner_info'] = $dinner_ar->toArray();
        $info['price_info'] = $this->_dinner_set_ar->toArray();
        return $info;
    }

    /**
     * 获取产品预留数量
     * @return int
     */
    public function getReserveNum()
    {
        if(empty($this->_dinner_set_ar)) return 0;
        else return $this->_dinner_set_ar->dinner_num;
    }

    /**
     * 获取产品剩余数量
     * @return int
     */
    public function getRemainNum()
    {
        if(empty($this->_dinner_set_ar)) return 0;
        else return $this->_dinner_set_ar->dinner_surplus;
    }

    /**
     * 计算折后价格
     * @param $apply_num
     * @param $discount_data
     * @return bool|mixed
     */
    public function calcApplyPrice($apply_num, $discount_data)
    {
        if(empty($this->_dinner_set_ar)) return false;

        $total_price = $this->_dinner_set_ar->dinner_price * $apply_num;
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
        if(empty($this->_dinner_set_ar)) return false;

        try{
            $oldNum = $this->_dinner_set_ar->dinner_num;
            $ups = intval($new_amount) - $oldNum;
            $this->_dinner_set_ar->dinner_num = $new_amount;
            if($this->_dinner_set_ar->save() && $this->_dinner_set_ar->updateCounters(['dinner_surplus'=>$ups])){
                return true;
            }else{
                throw new Exception('调整剩余数量失败');
            }
        }catch(Exception $e){
            $this->_pushError(0, 5000, $e->getMessage(), $this->_dinner_set_ar->getErrors());
            return false;
        }
    }

    /**
     * 增加剩余数量
     * @param $force
     * @param $num
     * @return false|array
     */
    public function incRemainNum($num, $force=true)
    {
        if(empty($this->_dinner_set_ar)) return false;
        try{
            if($this->_dinner_set_ar->updateCounters(['dinner_surplus'=>$num])){
                return true;
            }else{
                throw new Exception(json_encode($this->_dinner_set_ar->getErrors()));
            }
        }catch(Exception $e){
            $this->_pushError(0, 5000, '调整预留数量失败', $e->getMessage());
            return false;
        }
    }

    /**
     * 减少库存数量
     * @param $force
     * @param $num
     * @return array|false
     */
    public function decRemainNum($num, $force=false)
    {
        return $this->incRemainNum(-$num, $force);
    }
}