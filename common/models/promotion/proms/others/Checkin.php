<?php

namespace common\models\promotion\proms\others;

use common\models\ar\PromotionActivityCheckin as CheckinAR;

/**
 * 签到活动
 * Class Checkin
 * @package common\models\promotion\proms\others
 */
class Checkin
{
    /**
     * @var CheckinAR
     */
    protected $_checkin_ar;

    /**
     * 构造函数
     * Checkin constructor.
     * @param $checkin_ar
     */
    public function __construct($checkin_ar)
    {
        $this->_checkin_ar = $checkin_ar;
    }

    /**
     * 获取实例
     * @param $group_id
     * @param $hotel_id
     * @return Checkin|null
     */
    public static function getInstance($group_id, $hotel_id)
    {
        $checkin_ar = CheckinAR::findOne(['g_id'=>(int)$group_id, 'h_id'=>(int)$hotel_id]);
        if(empty($checkin_ar)){
            $ar = new CheckinAR();
            $ar->g_id = (int)$group_id;
            $ar->h_id = (int)$hotel_id;
            $ar->check_type = 1;
            $ar->integral_reward = 0;
            if($ar->save()){
                $checkin_ar = $ar;
            }else{
                return null;
            }
        }

        return new Checkin($checkin_ar);
    }

    /**
     * 获取活动信息
     * @return array|null
     */
    public function getInfo()
    {
        if(empty($this->_checkin_ar)) return null;
        return $this->_checkin_ar->toArray();
    }

    /**
     * 设置活动信息
     * @param $check_type
     * @param $integral
     * @param string $name
     * @param string $brief
     * @return bool
     */
    public function set($check_type, $integral, $name='', $brief='')
    {
        if(empty($this->_checkin_ar)) return false;
        if(!in_array($check_type, [1,2])) return false;

        $this->_checkin_ar->check_type = $check_type;
        $this->_checkin_ar->integral_reward = $integral;
        $this->_checkin_ar->name = $name;
        $this->_checkin_ar->brief = $brief;
        return $this->_checkin_ar->save();
    }
}