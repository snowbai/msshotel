<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class MemberGrade extends ActiveRecord{
    public static function tableName(){
        return '{{%member_grade}}';
    }

    public function rules(){
        return [
            [['start_grow','end_grow','g_id','h_id','cost'],'integer'],
            [['member_grade','grow_up','money_up'],'string'],
        ];
    }

    //获取会员等级列表
    public function selectall($g_id){
        return $this->find()
                     ->where("g_id=:g_id",[":g_id"=>$g_id])
                     ->orderBy("start_grow asc")
                     ->asArray()
                     ->all();
    }

    //获取会员的最高等级
    public function selectlast($g_id){
        return $this->find()
                     ->where("g_id=:g_id",[":g_id"=>$g_id])
                     ->orderBy("start_grow desc")
                     ->one();
    }

    //获取会员的最低等级
    public function selectfirst($g_id){
        return $this->find()
            ->where("g_id=:g_id",[":g_id"=>$g_id])
            ->orderBy("start_grow asc")
            ->one();
    }

    //添加数据
    public function add($p){
        $this->setAttributes($p);

        return $this->save();
    }

    //删除数据
    public function del($id){
        $one=$this->findOne($id);
        if($one){
            return $one->delete();
        }else{
            return false;
        }
    }

    //获取一条会员等级信息
    public function selectonegrade($id){
        return $this->find()
                     ->where('id=:id',[':id'=>$id])
                     ->asArray()
                     ->one();
    }

    //修改数据
    public function edit($p){
        $result=$this->findOne($p['id']);
        if(!$result){
            return false;
        }
        $result->member_grade=(string)$p["member_grade"];
        $result->start_grow=(int)$p["start_grow"];
        $result->end_grow=(int)$p["end_grow"];
        $result->money_up=(string)$p["money_up"];
        $result->cost=(int)$p["cost"];
        return $result->save();
    }

}


?>