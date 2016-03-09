<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class MdMember extends ActiveRecord{
    public static function tableName(){
        return '{{%member_all}}';
    }

    public function rules(){
        return [
            [["member_phone","member_realname"],"required"],
            [['member_phone','member_realname','add_time'],'string'],
        ];
    }

    //获取数据
    public function selectone($member_phone){
        return $this->find()
                     ->where("member_phone=:member_phone",[":member_phone"=>$member_phone])
                     ->one();
    }

    //添加数据
    public function add($member_phone,$member_realname){
        $this->member_phone=$member_phone;
        $this->member_realname=$member_realname;
        $this->add_time=date("Y-m-d H:i:s");
        return $this->save();
    }
}

?>