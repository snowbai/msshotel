<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;

class Microcard extends ActiveRecord{
    public static function tableName(){
        return '{{%microcard}}';
    }

    public function rules(){
        return [
            [['member_id','credits','grow_num','g_id','h_id'],'integer'],
            [['card','is_active','add_time','modify_time','end_time'],'string'],
        ];
    }

    //检测会员卡是否存在
    public function existsmicrocard($member_id){
        return $this->find()
                     ->where("member_id=:member_id",[":member_id"=>$member_id])
                     ->one();
    }
}

?>