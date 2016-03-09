<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class SysMemberGradeConfig extends ActiveRecord{
    public static function tableName(){
        return '{{%sys_member_grade_config}}';
    }

    public function rules(){
        return [

        ];
    }

    //获取模板全部信息
    /*
     * $type 模板id
     * */
    public function template_list($type){
        return $this->find()
                     ->where("type=:type",[":type"=>$type])
                     ->asArray()
                     ->all();
    }
}

?>