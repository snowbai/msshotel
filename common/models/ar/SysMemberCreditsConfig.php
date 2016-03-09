<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class SysMemberCreditsConfig extends ActiveRecord{
    public static function tableName(){
        return '{{%sys_member_credits_config}}';
    }

    public function rules(){
        return [

        ];
    }

    //获取所有成长值，积分获取条件 (系统初始值)
    public function selectall(){
        return $this->find()
                     //->where("type=:type",[":type"=>"1"])
                     ->asArray()
                     ->all();
    }

    //获取所有成长值，积分支出条件(系统初始值)
    public function selectallout(){
        return $this->find()
                     //->where("type=:type",[":type"=>"2"])
                     ->asArray()
                     ->all();
    }

    //获取一条数据
    public function selectone($sys_id){
        return $this->find()
                     ->where("sys_id=:sys_id",[":sys_id"=>$sys_id])
                     ->asArray()
                     ->one();
    }

}

?>