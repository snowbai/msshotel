<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class RoomWeekend extends ActiveRecord{
    public static function tableName(){
        return '{{%room_weekend}}';
    }

    public function rules(){
        return [
            [['g_id','h_id'],'integer'],
            [['weekend'],'safe'],
        ];
    }

    //酒店周末定义
    public function getweekend($g_id,$h_id){
        $result=$this->find()
            ->where('g_id=:g_id and h_id=:h_id',[':g_id'=>$g_id,':h_id'=>$h_id])
            ->asArray()
            ->one();
        return $result;
    }

    //添加周末定义
    public function add($p){
        $this->setAttributes($p);
        return $this->save();
    }

    //修改周末定义
    public function _edit($p){
        $result=$this->find()
                     ->where('g_id=:g_id and h_id=:h_id',[':g_id'=>$p['g_id'],':h_id'=>$p['h_id']])
                     ->one();
        $result->weekend=$p["weekend"];
        return $result->save();
    }

}

?>