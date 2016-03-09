<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class DinnerSet extends ActiveRecord{
    public static function tableName(){
        return '{{%dinner_set}}';
    }

    public function rules(){
        return [

        ];
    }

    //判断餐饮是否有剩余量
    public function selectnum($dinner_id,$date){
        return $this->find()
            ->where('dinner_id=:dinner_id and date>=:date and dinner_surplus>0',[':dinner_id'=>$dinner_id,':date'=>$date])
            ->asArray()
            ->one();
    }

    //获取一天的餐饮的价格和预留数
    public function selectdaynum($dinner_id,$date){
        return $this->find()
                     ->where('dinner_id=:dinner_id and date=:date',[':dinner_id'=>$dinner_id,':date'=>$date])
                     ->asArray()
                     ->one();
    }
}

?>