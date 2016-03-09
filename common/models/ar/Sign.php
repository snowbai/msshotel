<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class Sign extends ActiveRecord{
    public static function tableName(){
        return '{{%sign}}';
    }

    public function rules(){
        return [
            [["member_id","g_id","sign","time"],"required"],
        ];
    }

    //添加数据
    /*
     *
     * */
    public function add($member_id,$g_id,$sign,$time){
        $this->member_id=$member_id;
        $this->g_id=$g_id;
        $this->sign=$sign;
        $this->time=$time;

        return $this->save();
    }

    //查找会员签到数据时候已经存在
    public function selectone($member_id,$time){
        return $this->find()
                     ->where('member_id=:member_id and time=:time',[':member_id'=>$member_id,':time'=>$time])
                     ->one();
    }

}
?>