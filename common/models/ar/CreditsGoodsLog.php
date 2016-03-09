<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class CreditsGoodsLog extends ActiveRecord{
    public static function tableName(){
        return '{{%credits_goodslog}}';
    }

    public function rules(){
        return [
            [['member_id','good_id','credits','operator_id','exchange_num','g_id','h_id'],'integer'],
            [['is_confirm','add_time','address','phone','confirm_time'],'string'],

        ];
    }

    //新增记录
    public function add($p){
        /*$this->member_id=$p["member_id"];
        $this->good_id=$p["good_id"];
        $this->g_id=$p["g_id"];
        $this->credits=$p["credits"];
        $this->operator_id=$p['operator_id'];
        $this->is_confirm=$p['is_confirm'];
        $this->add_time=date("Y-m-d H:i:s");
        $this->h_id=$p["h_id"];*/
        $this->setAttributes($p);
        return $this->save();
    }

    //获取礼品的兑换数量
    public function excahnge_num($good_id){
        return $this->find()
                     ->where("good_id=:good_id and is_confirm!='2'",[":good_id"=>$good_id])
                     ->count();
    }

    //获取会员是否兑换过物品(前台兑换礼品时使用)
    public function member_exchange_log($member_id,$good_id){
        return $this->find()
                     ->where('member_id=:member_id and good_id=:good_id',[':member_id'=>$member_id,':good_id'=>$good_id])
                     ->count();
    }

    //获取已经兑换的积分
    public function usedcredits($member_id,$g_id,$h_id){
        return $this->find()
                     ->where('member_id=:member_id and g_id=:g_id',[':member_id'=>$member_id,':g_id'=>$g_id])
                     ->asArray()
                     ->all();
    }
}

?>