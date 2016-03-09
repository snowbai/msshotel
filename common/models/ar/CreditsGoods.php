<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class CreditsGoods extends ActiveRecord{

    public $num=0;//剩余数量

    public static function tableName(){
        return '{{%credits_goods}}';
    }

    public function rules(){
        return [

        ];
    }

    //获取所有没屏蔽的记录
    public function selectall($g_id){
        return $this->find()
                     ->where("g_id=:g_id and status='1'",[":g_id"=>$g_id])
                     ->orderBy("credits asc")
                     ->asArray()
                     ->all();
    }

    //获取一条记录
    public function selectone($id,$g_id){
        return $this->find()
                     ->where("id=:id and g_id=:g_id",[":id"=>$id,":g_id"=>$g_id])
                     ->one();
    }

}

?>