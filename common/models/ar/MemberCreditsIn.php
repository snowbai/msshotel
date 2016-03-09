<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class MemberCreditsIn extends ActiveRecord{
    public static function tableName(){
        return '{{%member_credits_in}}';
    }

    public function rules(){
        return [
            [['harvest','g_id','h_id','sys_id'],'integer'],
            [['project','harvest_type','reason','status'],'string'],
        ];
    }

    //新增一条数据
    public function add($p){
        $this->project=$p["project"];
        $this->harvest=$p["harvest"];
        $this->harvest_type=$p["harvest_type"];
        $this->reason=$p["reason"];
        $this->status=$p["status"];
        $this->g_id=$p["g_id"];
        $this->h_id=$p["h_id"];
        $this->sys_id=$p["sys_id"];

        if($this->save()){
            return $this;
        }else{
            return false;
        }
    }

    //获取酒店的积分和成长值的所有设置
    public function selectall($g_id){
        return $this->find()
                     ->where("g_id=:g_id",[":g_id"=>$g_id])
                     ->asArray()
                     ->all();
    }

    //获取酒店的积分和成长值的所有开启的设置
    public function selectopen($g_id){
        return $this->find()
                     ->where("g_id=:g_id and status=:status",[":g_id"=>$g_id,':status'=>'1'])
                     ->asArray()
                     ->all();
    }
    //改变状态
    /*
     * $id 数据库主键
     * $status 当前状态
     * */
    public function change($id,$status){
        if($status==1){
            $s='2';
        }else{
            $s='1';
        }

        $result=$this->findOne($id);
        if(!$result){
            return false;
        }
        $result->status=$s;

        return $result->save();
    }

    //修改积分配置
    public function edit($id,$harvest){
        $result=$this->findOne($id);
        if(!$result){
            return false;
        }
        $result->harvest=$harvest;
        return $result->save();
    }

    //获取一条未完成任务
    public function unfinish_one($id,$g_id,$h_id,$sys_id){
        if($id){
            return $this->find()
                         ->where("id not in({$id}) and status='1' and g_id=:g_id and sys_id in({$sys_id})",[":g_id"=>$g_id])
                         ->one();
        }else{
            return $this->find()
                         ->where("status='1' and g_id=:g_id and sys_id in({$sys_id})",[":g_id"=>$g_id])
                         ->one();
        }
    }

    //获取会员未完成的任务
    public function unfinish($id,$g_id,$h_id,$sys_id){
        if($id){
            return $this->find()
                         ->where("id not in({$id}) and status='1' and g_id=:g_id and sys_id in({$sys_id})",[":g_id"=>$g_id])
                         ->all();
        }else{
            return $this->find()
                         ->where("status='1' and g_id=:g_id and sys_id in({$sys_id})",[":g_id"=>$g_id])
                         ->all();
        }
    }

    //获取已完成任务
    public function finish($id){
        return $this->find()
                     ->where("id in({$id})")
                     ->all();
    }

    //获取一条积分任务
    public function task_one($task_id){
        return $this->findOne($task_id);
    }

    //获取一条积分配置 (添加时有用)
    public function selectone($g_id,$sys_id){
        return $this->find()
                     ->where("g_id=:g_id and sys_id=:sys_id",[":g_id"=>$g_id,":sys_id"=>$sys_id])
                     ->one();
    }

}


?>