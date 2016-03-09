<?php

namespace common\models\order\products;

use yii\base\Exception;
use common\models\ar\RoomType as RoomTypeAR;
use common\models\ar\RoomRate as RoomRateAR;

/**
 * 普通活动产品类
 * Class Room
 * @package common\models\order\products
 */
class Room extends OrderableBase implements IOrderable
{
    /**
     * 客房价格记录数组
     * @var array|\yii\db\ActiveRecord[]
     */
    protected $_room_rate_ars;

    /**
     * 入住日期
     * @var int
     */
    protected $_arrive_date;

    /**
     * 离店时间
     * @var int
     */
    protected $_leave_date;

    /**
     * 早餐类型
     * @var
     */
    protected $_breakfast_type;

    /**
     * 外加订购早餐数量
     * @var int
     */
    protected $_breakfast_num;

    /**
     * 构造函数
     * Room constructor.
     * @param $product_id
     * @param $attr
     * @param $room_rate_ars
     */
    protected function __construct($product_id, $attr, $room_rate_ars)
    {
        parent::__construct($product_id, $attr);
        $this->_arrive_date = isset($attr['apply_arrive_date']) ? strtotime($attr['apply_arrive_date']) : strtotime(date('Y-m-d'));
        $this->_leave_date = isset($attr['apply_leave_date']) ? strtotime($attr['apply_leave_date']) : strtotime('+1 day',$this->_arrive_date);
        $this->_breakfast_type = isset($attr['breakfast_type']) ? (int) $attr['breakfast_type'] : 0;
        $this->_breakfast_num = isset($attr['apply_breakfast_num']) ? (int) $attr['apply_breakfast_num'] : 0;
        $this->_room_rate_ars = $room_rate_ars;
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
        $leave_date = isset($attr['apply_leave_date']) ? strtotime($attr['apply_leave_date']) : strtotime('+1 day',$arrive_date);
        $room_rate_ars = RoomRateAR::find()->where(['room_id'=>$product_id])
            ->andWhere(['>=', 'date', $arrive_date])
            ->andWhere(['<', 'date', $leave_date])
            ->all();

        if(empty($room_rate_ars)) return null;
        return new Room($product_id, $attr, $room_rate_ars);
    }

    /**
     * 获取产品信息
     * @param $attr
     * @return array
     */
    public static function getTypeInfo($attr=null)
    {
        return ['type_name'=>'room'];
    }

    /**
     * 获取产品信息
     * @return null
     */
    public function getInfo()
    {
        $room_ar = RoomTypeAR::findOne($this->_product_id);
        if(empty($room_ar)) return null;

        $info['room_info'] = $room_ar->toArray();
        foreach($this->_room_rate_ars as $ar){
            $info['room_rate'][$ar->date]['zx_nobreakfast'] = $ar->zx_nobreakfast;
            $info['room_rate'][$ar->date]['zx_onebreakfast'] = $ar->zx_onebreakfast;
            $info['room_rate'][$ar->date]['zx_doublebreakfast'] = $ar->zx_doublebreakfast;
            $info['room_rate'][$ar->date]['breakfast_price'] = $ar->breakfast_price;
        }
        return $info;
    }

    /**
     * 获取产品预留数量
     * @return int
     */
    public function getReserveNum()
    {
        $total_days = (int) (($this->_leave_date-$this->_arrive_date)/86400);
        if($total_days <=0 || $total_days != count($this->_room_rate_ars)){
            return 0;
        }

        $min = 999;
        foreach($this->_room_rate_ars as $day_rate_ar){
            if($day_rate_ar->room_num < $min) $min = $day_rate_ar->room_num;
        }

        return $min;
    }

    /**
     * 获取产品剩余数量
     * @return int
     */
    public function getRemainNum()
    {
        $total_days = (int) (($this->_leave_date-$this->_arrive_date)/86400);
        if($total_days <=0 || $total_days != count($this->_room_rate_ars)){
            return 0;
        }

        $min = 999;
        foreach($this->_room_rate_ars as $day_rate_ar){
            if($day_rate_ar->room_surplus < $min) $min = $day_rate_ar->room_surplus;
        }

        return $min;
    }

    /**
     * 计算折后价格
     * @param $apply_num
     * @param $discount_data
     * @return bool|mixed
     */
    public function calcApplyPrice($apply_num, $discount_data)
    {
        $total_days = (int) (($this->_leave_date-$this->_arrive_date)/86400);
        if($total_days <=0 || $total_days != count($this->_room_rate_ars)){
            return false;
        }

        $total_price = 0;
        foreach($this->_room_rate_ars as $day_rate_ar){
            switch($this->_breakfast_type){
                case 0:
                    $room_price = $day_rate_ar->zx_nobreakfast * $apply_num;
                    break;
                case 1:
                    $room_price = $day_rate_ar->zx_onebreakfast * $apply_num;
                    break;
                case 2:
                    $room_price = $day_rate_ar->zx_doublebreakfast * $apply_num;
                    break;
                default:
                    $room_price = $day_rate_ar->zx_nobreakfast * $apply_num;
            }
            $breakfast_price = $day_rate_ar->breakfast_price * $this->_breakfast_num;
            $total_price += $room_price + $breakfast_price;
        }

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
        $total_days = (int) (($this->_leave_date-$this->_arrive_date)/86400);
        if($total_days <=0 || $total_days != count($this->_room_rate_ars)){
            return false;
        }

        try{
            foreach($this->_room_rate_ars as $day_rate_ar){
                $oldNum = $day_rate_ar->room_num;
                $ups = intval($new_amount) - $oldNum;
                $day_rate_ar->room_surplus = $new_amount;

                if($day_rate_ar->save() && $day_rate_ar->updateCounters(['room_surplus'=>$ups])){
                    return true;
                }else{
                    throw new Exception(json_encode($day_rate_ar->getErrors()));
                }
            }
            return true;
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
        $total_days = (int) (($this->_leave_date - $this->_arrive_date)/86400);
        if($total_days <=0 || $total_days != count($this->_room_rate_ars)){
            $this->_pushError(0, 5000, '预定日期内客房量不足'.date('Y-m-d',$this->_leave_date).date('Y-m-d',$this->_arrive_date).json_encode($this->_attr), null);
            return false;
        }

        if(!$force){
            $room_type_ar = $room_ar = RoomTypeAR::findOne($this->_product_id);
            if(empty($room_type_ar) || $room_type_ar->status==2){
                $this->_pushError(0, 5000, '客房被关闭', null);
                return false;
            }
        }

        try{
            foreach($this->_room_rate_ars as $day_rate_ar){
                if(!$force && $day_rate_ar->room_status == 2){
                    throw new Exception('当天客房被关闭');
                }
                if(!$day_rate_ar->updateCounters(['room_surplus'=>$num])){
                    throw new Exception(json_encode($day_rate_ar->getErrors()));
                }
            }
            return true;
        }catch(Exception $e){
            $this->_pushError(0, 5000, '调整剩余量失败', $e->getMessage());
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