<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class GrowLog extends ActiveRecord{
    public static function tableName(){
        return '{{%grow_log}}';
    }

    public function rules(){
        return [
            [["member_id","g_id","reason","grow_num"],"required"],
        ];
    }

    //添加记录
    /*
     *
     * */
    public function add($member_id,$g_id,$h_id,$reason,$grow_num,$operator_id){
        $this->member_id=$member_id;
        $this->g_id=$g_id;
        $this->h_id=$h_id;
        $this->reason=$reason;
        $this->grow_num=$grow_num;
        $this->add_time=date("Y-m-d H:i:s");
        $this->operator_id=$operator_id;

        return $this->save();
    }
}

?>