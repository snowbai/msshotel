<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class RoomType extends ActiveRecord{
    public static function tableName(){
        return '{{%room_type}}';
    }

    public function rules(){
        return [
            [['room_type','room_num','checkin_num','area','add_bed_price','g_id','h_id'],'integer'],
            [['room_name','floor','window','bed','add_bed','wifi','wifi_type','computer','feature','remark','reserve_time','status','bed_width'],'string'],
            //['bed_width','safe'],
        ];
    }

    //获取房间列表
    /*
     * $g_id    集团id/酒店id
     * $h_id    门店id
     * */
    public function selectall($g_id,$h_id){
        return $this->find()
                     ->where('g_id=:g_id and h_id=:h_id',[':g_id'=>$g_id,':h_id'=>$h_id])
                     ->asArray()
                     ->all();
    }


    //获取一条房间记录
    public function selectone($room_id,$g_id,$h_id){
        return $this->find()
                     ->where('room_id=:room_id and g_id=:g_id and h_id=:h_id',[':room_id'=>$room_id,':g_id'=>$g_id,':h_id'=>$h_id])
                     ->asArray()
                     ->one();
    }

    //添加房型
    public function add($p){
        $this->setAttributes($p);
        return $this->save();
    }

    //修改房型
    public function edit($p){
        $result=$this->findOne($p['room_id']);
        if($result){
            $result->setAttributes($p);
            return $result->save();
        }
        return false;
    }

    //删除房型
    /*
     * $room_id 房型id
     * */
    public function del($room_id,$g_id,$h_id){
        $result=$this->find()
                     ->where('room_id=:room_id and g_id=:g_id and h_id=:h_id',[':room_id'=>$room_id,':g_id'=>$g_id,':h_id'=>$h_id])
                     ->one();
        if($result){
            return $result->delete();
        }else{
            return false;
        }
    }

    //修改房型状态
    public function editstatus($p){
        $result=$this->findOne($p['room_id']);
        //return $result;
        if($result){
            $result->status=$p['status'];
            //$result->setAttributes($p,false);
            return $result->save();
        }
        return false;
    }
}

?>