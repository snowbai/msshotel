<?php

namespace common\models\mssgh\extensions;

/**
 * 扩展信息类
 * Class Theme
 * @package common\models\mssgh\extensions;
 */
class InfoExt extends Meta
{
    /**
     * 获取酒店政策
     * @return string
     */
    public function getPolicy()
    {
        return json_decode($this->getMetaData('hotel_policy'), true);
    }

    /**
     * 设置酒店政策
     * @param $data
     * @return bool
     */
    public function setPolicy($data)
    {
        $check_in_time = isset($data['check_in_time']) ? $data['check_in_time'] : null;
        $check_out_time = isset($data['check_out_time']) ? $data['check_out_time'] : null;
        $policy_data = ['check_in_time'=>$check_in_time, 'check_out_time'=>$check_out_time];
        return $this->setMetaData('hotel_policy',json_encode($policy_data));
    }
}