<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class MemberPrivilege extends ActiveRecord{
    public static function tableName(){
        return '{{%member_privilege}}';
    }

    public function rules(){
        return [
            [['member_grade','type','discount','credits','max_credits','g_id','h_id'],'integer'],
            [['discount_status','credits_status','max_credits_status'],'string'],
            [['discount','credits','max_credits'],'default','value'=>0],
        ];
    }

    //查找一行会员等级折扣
    /*
     * $g_id            集团id/酒店id
     * $member_grade    会员等级id
     * $type            模块 1客房  2自助餐 ...
     * */
    public function selectone($g_id,$member_grade,$type){
        return $this->find()
                     ->where("g_id=:g_id and member_grade=:member_grade and type=:type",[":g_id"=>$g_id,":member_grade"=>$member_grade,":type"=>$type]) //and discount_status=:discount_status   ,':discount_status'=>'1'
                     ->asArray()
                     ->one();
    }

    //设置配置
    public function set($p){
        $this->setAttributes($p);
        return $this->save();
    }

    //修改配置
    public function edit($p){
        $result=$this->find()
            ->where("g_id=:g_id and member_grade=:member_grade and type=:type",[":g_id"=>$p['g_id'],":member_grade"=>$p['member_grade'],":type"=>$p['type']])
            ->one();
        $result->setAttributes($p);
        return $result->save();
    }

    //删除配置
    /*
     * $member_grade_id     会员等级id
     * $g_id                g_id
     * */
    public function del($member_grade_id,$g_id){
        $result=$this->find()
                     ->where('member_grade=:member_grade and g_id=:g_id',[':member_grade'=>$member_grade_id,':g_id'=>$g_id])
                     ->all();
        if($result){
            return MemberPrivilege::deleteAll('member_grade=:member_grade and g_id=:g_id',[':member_grade'=>$member_grade_id,':g_id'=>$g_id]);
        }else{
            return true;
        }
    }
}

?>