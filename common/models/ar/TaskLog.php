<?php

namespace common\models\ar;
use yii\db\ActiveRecord;

class TaskLog extends ActiveRecord{
    public static function tableName(){
        return '{{%task_log}}';
    }

    public function rules(){
        return [
            [['task_id','member_id','credits','operator_id'],'integer'],
            [['is_confirm','add_time','confirm_time'],'string'],
        ];
    }

    //添加任务日志
    public function add($p){
        $this->task_id=$p["task_id"];
        $this->member_id=$p["member_id"];
        $this->credits=$p["credits"];
        $this->operator_id=0;
        $this->is_confirm='0';
        $this->add_time=date("Y-m-d H:i:s");

        return $this->save();
    }

    //获取已完成任务id
    public function finish($member_id){
        $fin=$this->find()
                  ->select("task_id")
                  ->where("member_id=:member_id and is_confirm!='2'",[":member_id"=>$member_id])
                  ->all();
        $arr=[];
        foreach($fin as $k=>$v){
            $arr[]=$v["task_id"];
        }

        return join(",",$arr);
    }

    //判断任务时候已经完成过
    public function is_finish($member_id,$task_id){
        return $this->find()
                     ->where("member_id=:member_id and task_id=:task_id",[":member_id"=>$member_id,":task_id"=>$task_id])
                     ->all();
    }
}

?>