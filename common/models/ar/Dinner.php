<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class Dinner extends ActiveRecord{
    public static function tableName(){
        return '{{%dinner}}';
    }

    public function rules(){
        return [

        ];
    }

    //获取所有开启的餐饮
    public function selectall($g_id,$h_id){
        return $this->find()
                     ->where('dinner_status=:dinner_status and g_id=:g_id',[':dinner_status'=>'1',':g_id'=>$g_id])
                     ->all();
    }

    //获取一条开启的餐饮的详情
    public function selectone($dinner_id){
        return $this->find()
                     ->where('dinner_id=:dinner_id and dinner_status=:dinner_status',[':dinner_status'=>'1','dinner_id'=>$dinner_id])
                     ->one();
    }

    /**
     * 根据时间生成列表
     * @param string $field 查询的开始时间
     * @return string
     */
    public function generateTimeList($field = 'am_start_time'){
        //return $this->$field;
        if(!$field || !$this->$field) return '';
        $pos = strpos($this->$field, ':');

        $start = intval(substr($this->$field, 0, $pos));
        $start_minute = intval(substr($this->$field, $pos+1));
        $minute = '00';
        switch ($start_minute) {
            case 0:
                $minute = 0;
                break;
            case ($start_minute > 0 && $start_minute <30):
                $minute = '30';
                break;
            default:
                $start += 1;
                break;
        }
        $end_time = explode(':', $field == 'pm_start_time' ? $this->pm_start_time : $this->pm_end_time);
        $end_minute = isset($end_time[1]) ? $end_time[1] : '';
        $count = $end_time[0];
        if($count < $start){
            //$start = $count;
            $count = 24 + $count;
        }
        $list = '';
        $hour = '';
        for($i = $start; $i <= $count; $i++)
        {
            $hour = sprintf('%02d', $i > 24 ? $i - 24 : $i);
            $minute = $minute ? $minute : '00';
            $list .= "<li><a href='javascript:;' >{$hour}:{$minute}点</a></li>";
            if($minute !== '30'){
                if($i == $count && $end_minute == '00') break;
                $list .= "<li><a href='javascript:;' >{$hour}:30点</a></li>";
            }
            $minute = false;
        }
        return $list;
    }

}

?>