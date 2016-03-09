<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class RoomRate extends ActiveRecord{
    public static function tableName(){
        return '{{%room_rate}}';
    }

    public function rules(){
        return [
            [['room_id','date','zx_nobreakfast','zx_onebreakfast','zx_doublebreakfast','room_num','room_surplus','g_id','h_id','breakfast_price',],'integer'],
            [['breakfast_type','ota_price','show_price','room_status',],'string'],
        ];
    }

    //设置价格
    public function set_price($p){
        $this->setAttributes($p);
        return $this->save();
    }

    //修改价格
    public function edit_price($p){
        $result=$this->find()
                     ->where("room_id=:room_id and date=:date",[":room_id"=>$p["room_id"],":date"=>$p["date"]])
                     ->one();
        $result->setAttributes($p);
        return $result->save();
    }

    //设置房量
    public function set_status($p){
        $this->setAttributes($p);
        return $this->save();
    }

    //修改房量
    public function edit_status($p){
        $result=$this->find()
            ->where("room_id=:room_id and date=:date",[":room_id"=>$p["room_id"],":date"=>$p["date"]])
            ->one();
        $result->room_status=(string)$p['room_status'];
        $result->room_num=(int)$p['room_num'];
        $result->room_surplus=(int)$p['room_surplus'];
        //return $result->attributes;
        return $result->save();
    }

    //查找一天的价格/房量
    /*
     * $room_id 房型id
     * $date    日期 时间戳
     * */
    public function get_price($room_id,$date){
        return $this->find()
                     ->where("room_id=:room_id and date=:date",[":room_id"=>$room_id,":date"=>$date])
                     ->asArray()
                     ->one();
    }
}

?>