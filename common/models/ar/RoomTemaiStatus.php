<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class RoomTemaiStatus extends ActiveRecord{
    public static function tableName(){
        return '{{%room_temai_status}}';
    }

    public function rules(){
        return [
            [['temai_id','date','temai_num','temai_surplus','temai_price','add_hour_price'],'integer'],
            [['discount_time_key','room_status','reserve_time','reason','add_hour'],'string'],
        ];
    }

    //设置特买房预留数
    public function set_reserve_num($p){
        $this->setAttributes($p);
        return $this->save();
    }

    //修改特买房的优惠时间段 key
    /*
     * $temai_id 特卖id
     * $old_key  老的优惠时间段key
     * $new_key  新的优惠时间段key
     * */
    public function update_discount_time_key($temai_id,$old_key,$new_key){
        return self::updateAll(['discount_time_key'=>$new_key],'temai_id=:temai_id and discount_time_key=:discount_time_key',[':temai_id'=>$temai_id,':discount_time_key'=>$old_key]);
    }

    //获取一段时间段里面的一个优惠时间段里面的预留数
    public function getnumbytime($start_time,$end_time,$temai_id,$key){
        return $this->find()
                     ->select('temai_id,date,discount_time_key,temai_num,temai_surplus')
                     ->where('date>=:start_time and date<=:end_time and temai_id=:temai_id and discount_time_key=:discount_time_key',[':start_time'=>$start_time,':end_time'=>$end_time,':temai_id'=>$temai_id,':discount_time_key'=>$key])
                     ->asArray()
                     ->all();
    }

    //获取一天中的某个优惠时间段的预留详情
    /*
     * $temai_id            特卖id
     * $date                日期
     * $discount_time_key   优惠时间段key
     * */
    public function get_day_reserve_num($temai_id,$date,$discount_time_key){
        return $this->find()
                     ->where('temai_id=:temai_id and date=:date and discount_time_key=:discount_time_key',[':temai_id'=>$temai_id,':date'=>$date,':discount_time_key'=>$discount_time_key])
                     ->one();
    }

    //获取特买房预留时间段的预留数
    /*
     * $temai_id            特卖id
     * $discount_time_key   优惠时间段key
     * */
    public function get_reserve_num($temai_id,$discount_time_key){
        return $this->find()
                     ->where('temai_id=:temai_id and discount_time_key=:discount_time_key',[':temai_id'=>$temai_id,':discount_time_key'=>$discount_time_key])
                     ->asArray()
                     ->all();
    }

    //获取一段时间里面一个特买房的详情
    /*
     * $temai_id        特卖id
     * $start_time      开始时间
     * $end_time        结束时间
     * */
    public function temai_surplus($temai_id,$start_time,$end_time){
        return $this->find()
                     ->where('temai_id=:temai_id and date>=:start_time and date<=:end_time',[':temai_id'=>$temai_id,':start_time'=>$start_time,':end_time'=>$end_time])
                     ->all();
    }

   /* //获取一段优惠时间段里面的特买房详情
    public function temai_discount_surplus($temai_id,$key){
        return $this->find()
                     ->where('temai_id=:temai_id and discount_time_key=:discount_time_key',[':temai_id'=>$temai_id,':discount_time_key'=>$key])
                     ->all();
    }*/

    //删除不在活动时间里面的数据
    /*
     * $stime       开始时间
     * $etime       结束时间
     * $temai_id    特卖id
     * */
    public function delete_status($stime,$etime,$temai_id){
        $row=$this->find()
                  ->where('temai_id=:temai_id and date>=:stime and date<:etime',[':stime'=>$stime,':etime'=>$etime,':temai_id'=>$temai_id])
                  ->all();
        if($row){
            return self::deleteAll('temai_id=:temai_id and date>=:stime and date<:etime',[':stime'=>$stime,':etime'=>$etime,':temai_id'=>$temai_id]);
        }
        return true;
    }

    //删除一段优惠时间段的所有房态
    /*
     * $temai_id    特卖id
     * $key         优惠时间段key
     * */
    public function delete_discount_status($temai_id,$key){
        return self::deleteAll('temai_id=:temai_id and discount_time_key=:discount_time_key',[':temai_id'=>$temai_id,':discount_time_key'=>$key]);
    }
}

?>