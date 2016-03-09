<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class MemberLoginLog extends ActiveRecord{
    public static function tableName(){
        return '{{%member_login_log}}';
    }

    public function rules(){
        return [

        ];
    }

    //添加数据
    public function add($p){
        $this->member_id=$p["member_id"];
        $this->g_id=$p["g_id"];
        $this->login_time=date("Y-m-d H:i:s");
        $this->member_name=$p["member_name"];
        $this->h_id=$p["h_id"];
        return $this->save();
    }


}

?>