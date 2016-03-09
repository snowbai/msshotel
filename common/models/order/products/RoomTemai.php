<?php

namespace common\models\order\products;

use yii\base\Exception;
use common\models\ar\RoomType as RoomTypeAR;
use common\models\ar\RoomTemai as TemaiAR;
use common\models\ar\RoomTemaiStatus as TemaiRateAR;

/**
 * 特卖客房产品类
 * Class RoomTemai
 * @package common\models\order\products
 */
class RoomTemai extends OrderableBase implements IOrderable
{
    /**
     * 客房价格记录数组
     * @var TemaiRateAR
     */
    protected $_rate_ar;

    /**
     * 添加小时数量
     * @var
     */
    protected $_add_hours;

    /**
     * 构造函数
     * Room constructor.
     * @param $temai_id
     * @param $attr
     * @param $rate_ar
     */
    protected function __construct($temai_id, $attr, $rate_ar)
    {
        parent::__construct($temai_id, $attr);
        $this->_add_hours = isset($attr['apply_add_hours']) ? (int) $attr['apply_add_hours'] : 0;
        $this->_rate_ar = $rate_ar;
    }

    /**
     * 获取对象
     * @param $temai_id
     * @param $attr
     * @return Room|null
     */
    public static function getInstance($temai_id, $attr)
    {
        $date = isset($attr['temai_date']) ? strtotime($attr['temai_date']) : strtotime('0000-00-00');
        $key = isset($attr['temai_key']) ? (string)$attr['temai_key'] : '';
        $rate_ar = TemaiRateAR::find()
            ->where(['temai_id'=>$temai_id, 'date'=>$date,'discount_time_key'=>$key])
            ->one();
        if(empty($rate_ar)) return null;
        else return new RoomTemai($temai_id, $attr, $rate_ar);
    }

    /**
     * 获取产品信息
     * @param $attr
     * @return array
     */
    public static function getTypeInfo($attr=null)
    {
        return ['type_name'=>'roomtemai'];
    }

    /**
     * 获取产品信息
     * @return null
     */
    public function getInfo()
    {
        $temai_ar = TemaiAR::findOne($this->_product_id);
        if(empty($temai_ar)) return null;
        $room_ar = RoomTypeAR::findOne($temai_ar->room_id);
        if(empty($room_ar)) return null;

        $info = $temai_ar->toArray();
        $info['room_name'] = $room_ar->room_name;
        return $info;
    }

    /**
     * 获取产品预留数量
     * @return int
     */
    public function getReserveNum()
    {
        if(empty($this->_rate_ar)) return 0;
        else return $this->_rate_ar->temai_num;
    }

    /**
     * 获取产品剩余数量
     * @return int
     */
    public function getRemainNum()
    {
        if(empty($this->_rate_ar)) return 0;
        else return $this->_rate_ar->temai_surplus;
    }

    /**
     * 计算折后价格
     * @param $apply_num
     * @param $discount_data
     * @return bool|mixed
     */
    public function calcApplyPrice($apply_num, $discount_data)
    {
        if(empty($this->_rate_ar)) return false;
        $total_price = ($this->_rate_ar->temai_price + $this->_rate_ar->add_hour_price*$this->_add_hours) * $apply_num;
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
        if(empty($this->_rate_ar)) return false;
        try{
            $oldNum = $this->_rate_ar->temai_num;
            $ups = intval($new_amount) - $oldNum;
            $this->_rate_ar->temai_num = $new_amount;

            if($this->_rate_ar->save() && $this->_rate_ar->updateCounters(['temai_surplus'=>$ups])){
                return true;
            }else{
                throw new Exception(json_encode($this->_rate_ar->getErrors()));
            }
        }catch(Exception $e){
            $this->_pushError(0, 5000, '调整预留数量失败', $e->getMessage());
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
        if(empty($this->_rate_ar)) return false;
        try{
            if($this->_rate_ar->updateCounters(['temai_surplus'=>$num])){
                return true;
            }else{
                throw new Exception(json_encode($this->_rate_ar->getErrors()));
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