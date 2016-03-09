<?php

namespace common\models\ar;
use yii\db\ActiveRecord;
use yii\db\Query;

class Room extends ActiveRecord{
    public static function tableName(){
        return '{{%room_rate}}';
    }

    public function rules(){
        return [

        ];
    }

    //获取酒店房价和房态列表
    /*
     * $date        日期
     * $g_id        酒店或集团id
     * $h_id        门店id
     * $room_id     房型id
     * */
    public function roomlist($date,$room_id){
        $query=new Query();
        return $query->select('rr.*,rt.status')
            ->from('room_rate as rr')
            ->leftJoin('room_type as rt','rr.room_id=rt.room_id')
            ->where('rr.date=:date and rr.room_id=:room_id',[':date'=>$date,':room_id'=>$room_id])
            ->one();
    }

    //获取酒店详情
    /*
     * $date        日期
     * $g_id        酒店或集团id
     * $h_id        门店id
     * $room_id     房型id
     * */
    public function roomdetial($date,$room_id){
        $query=new Query();
        return $query->select('rt.*,rr.zx_nobreakfast,rr.zx_onebreakfast,rr.zx_doublebreakfast,rr.breakfast_type,rr.breakfast_price,rr.ota_price,rr.show_price')
            ->from('md_room_rate as rr')
            ->leftJoin('md_room_type as rt','rr.room_id=rt.room_id')
            ->where('rr.date=:date and rr.room_id=:room_id',[':date'=>$date,':room_id'=>$room_id])
            ->one();

    }


}

?>