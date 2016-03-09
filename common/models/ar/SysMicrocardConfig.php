<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class SysMicrocardConfig extends ActiveRecord{
    public static function tableName(){
        return '{{%sys_microcard_config}}';
    }

    public function rules(){
        return [
            [['money','g_id','h_id'],'integer'],
            [['get_type'],'string'],
            [['money'],'default','value'=>0],
        ];
    }

    //查找会员卡配置
    public function selectone($g_id){
        return $this->find()
                     ->where("g_id=:g_id",[":g_id"=>$g_id])
                     ->asArray()
                     ->one();
    }

    //添加会员卡配置
    public function add($p){
        $this->get_type= $p["get_type"];
        $this->money= $p["money"];
        $this->g_id=$p["g_id"];
        $this->h_id=$p["h_id"];
        return $this->save();
    }

    //修改会员卡配置
    public function edit($p){
        $result=$this->find()
                     ->where("g_id=:g_id",[":g_id"=>$p['g_id']])
                     ->one();
        $result->get_type=$p["get_type"];
        $result->money=$p["money"];
        return $result->save();
    }
}

?>