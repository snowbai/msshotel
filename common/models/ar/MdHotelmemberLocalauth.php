<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class MdHotelmemberLocalauth extends ActiveRecord{
    public static function tableName(){
        return '{{%hotelmember_localauth}}';
    }

    public function rules(){
        return [
            [['member_id','g_id','h_id'],'integer'],
            [['member_phone','member_pwd'],'string'],
        ];
    }

    public function add($p){
        $this->setAttributes($p);
        return $this->save();
    }

    //修改密码
    public function editpwd($member_id,$pwd){
        $result=$this->find()
             ->where('member_id=:member_id',[':member_id'=>$member_id])
             ->one();
        if($result){
            $result->member_pwd=$pwd;
            return $result->save();
        }else{
            return false;
        }
    }

    //忘记密码时修改
    public function setpwd($member_phone,$g_id,$pwd){
        $result=$this->find()
                     ->where('member_phone=:member_phone and g_id=:g_id',[':member_phone'=>$member_phone,':g_id'=>$g_id])
                     ->one();
        if($result){
            $result->member_pwd=$pwd;
            return $result->save();
        }else{
            return false;
        }
    }
}

?>