<?php
namespace common\models\ar;

use yii\db\ActiveRecord;

class CreditsLog extends ActiveRecord{
    public static function tableName(){
        return '{{%credits_log}}';
    }

    public function rules(){
        return [
            [["member_id","g_id","credits","reason","method"],"required"],
        ];
    }

    //新增记录
    /*
     * $member_id 会员id
     * $credits   积分
     * $good_id   礼品id
     * $g_id      集团id/酒店id
     * $h_id      门店id
     * $reason    积分原因
     * $method    操作方法
     * $task_id   积分任务id
     * */
    public function add($member_id,$credits,$g_id,$h_id=0,$reason='',$method,$operator_id){
        $this->member_id=$member_id;
        $this->credits=$credits;
        $this->g_id=$g_id;
        $this->reason=$reason;
        $this->method=$method;
        $this->add_time=date("Y-m-d H:i:s");
        $this->h_id=$h_id;
        $this->operator_id=$operator_id;

        return $this->save();
    }

    //查看积分明细
    public function detial($member_id,$g_id,$h_id){
        return $this->find()
                     ->where("member_id=:member_id and g_id=:g_id",[":member_id"=>$member_id,":g_id"=>$g_id])
                     ->orderBy("add_time desc")
                     ->all();
    }

    //积分获取明细
    public function detialin($member_id,$g_id,$h_id){
        return $this->find()
                     ->where("member_id=:member_id and g_id=:g_id and method='1'",[":member_id"=>$member_id,":g_id"=>$g_id])
                     ->orderBy("add_time desc")
                     ->all();
    }

    //积分支出明细
    public function detialout($member_id,$g_id,$h_id){
        return $this->find()
                     ->where("member_id=:member_id and g_id=:g_id and method='2'",[":member_id"=>$member_id,":g_id"=>$g_id])
                     ->orderBy("add_time desc")
                     ->all();
    }

}

?>