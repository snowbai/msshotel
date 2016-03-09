<?php

namespace common\models\mssgh\extensions;

use common\models\mssgh\MssghConst;

/**
 * 酒店设施类
 * Class Facility
 * @package common\models\mssgh\extensions;
 */
class Facility extends Meta
{
    /**
     * 获取酒店特色信息
     * @return array 格式[type=>['name'=>feature_name, 'enable'=>bool]]
     */
    public function getFeature()
    {
        return $this->_getData('hotel_features', MssghConst::HOTEL_DEFAULT_FACILITY_FEATURES);
    }

    /**
     * 获取酒店服务信息
     * @return string
     */
    public function getService()
    {
        return $this->_getData('hotel_services', MssghConst::HOTEL_DEFAULT_FACILITY_SERVICES);
    }

    /**
     * 获取酒店设施
     * @return string
     */
    public function getFacility()
    {
        return $this->_getData('hotel_facilities', MssghConst::HOTEL_DEFAULT_FACILITY_FACILITIES);
    }

    /**
     * 设置酒店特色信息
     * @param $data, 格式[type=>['name'=>feature_name, 'enable'=>bool]]
     * @return bool
     */
    public function setFeature($data)
    {
        return $this->_setData('hotel_features', $data);
    }

    /**
     * 设置酒店服务信息
     * @param $data
     * @return bool
     */
    public function setService($data)
    {
        return $this->_setData('hotel_services', $data);
    }

    /**
     * 设置酒店设施信息
     * @param $data
     * @return bool
     */
    public function setFacility($data)
    {
        return $this->_setData('hotel_facilities', $data);
    }

    /**
     * 获取设施数据
     * @param $meta_key
     * @param $def_array
     * @return array
     */
    protected function _getData($meta_key, $def_array)
    {
        $result = array();
        foreach($def_array as $type=>$item)
        {
            $item['enable'] = false;
            $result[$type] = $item;
        }

        $meta_value = $this->getMetaData($meta_key);
        if(!empty($meta_value)){
            $cus_data = json_decode($meta_value, true);
            if(is_array($cus_data)){
                foreach($cus_data as $type=>$item){
                    $item['enable'] = true;
                    $result[$type] = $item;
                }
            }
        }

        return $result;
    }

    /**
     * 设置设施数据
     * @param $meta_key
     * @param $data
     * @return bool
     */
    protected function _setData($meta_key, $data)
    {
        if(!is_array($data)){
            return false;
        }

        $cus_data = array();
        foreach($data as $type=>$item){
            if($item['enable']==true){
                $cus_data[$type] = ['name'=>$item['name']];
            }
        }
        return $this->setMetaData($meta_key, json_encode($cus_data));
    }
}