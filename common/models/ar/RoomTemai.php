<?php

namespace common\models\ar;
use yii\db\ActiveRecord;
use yii\db\Query;

class RoomTemai extends ActiveRecord{
    public static function tableName(){
        return '{{%room_temai}}';
    }

    public function rules(){
        return [
            [['room_id','start_time','end_time','g_id','h_id'],'integer'],
            [['room_type','discount_time','room_status'],'string']
        ];
    }

    //特买房后是否存在
    public function isset_temai($temai_id){
        return $this->find()
                     ->where('temai_id=:temai_id',[':temai_id'=>$temai_id])
                     ->one();
    }

    //获取一个上架房型的活动时间
    /*
     * $room_id     房型id
     * $temai_id    特买房id
     * */
    public function temai_time($room_id,$temai_id){
        if($temai_id){
            return $this->find()
                ->where('room_id=:room_id and room_status=:room_status and temai_id!=:temai_id',[':room_id'=>$room_id,':room_status'=>'1',':temai_id'=>$temai_id])
                ->asArray()
                ->all();
        }
        return $this->find()
                     ->where('room_id=:room_id and room_status=:room_status',[':room_id'=>$room_id,':room_status'=>'1'])
                     ->asArray()
                     ->all();
    }

    //特买房列表
    public function temailist($g_id,$h_id,$search=[]){
        $query=new Query();
        $result=$query->select('rtemai.*,rtype.room_name')
                      ->from('md_room_temai as rtemai')
                      ->leftJoin('md_room_type as rtype','rtemai.room_id=rtype.room_id')
                      ->where('rtemai.g_id=:g_id and rtemai.h_id=:h_id',[':g_id'=>$g_id,':h_id'=>$h_id]);
                    if($search['room_type']!=0){
                        $result->andWhere('rtemai.room_type=:room_type',[':room_type'=>$search['room_type']]);
                    }
        return $result->all();
    }

    //设置特买房
    public function set_temai($p){
        $this->setAttributes($p);
        if($this->save()){
            return $this;
        }else{
            return false;
        }
    }

    //修改特买房
    public function edit_temai($p,$temai_id){
        $row=$this->find()
                  ->where('temai_id=:temai_id',[':temai_id'=>$temai_id])
                  ->one();
        $row->setAttributes($p);
        if($row->save()){
            return $row;
        }else{
            return false;
        }
    }

    //修改特买房状态
    public function change_status($temai_id,$status){
        $row=$this->findOne($temai_id);
        $row->room_status=(string)$status;
        //return $row;
        return $row->save();
    }
}

?>