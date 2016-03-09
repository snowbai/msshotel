<?php

namespace common\models\mssgh\extensions;

use common\models\ar\MssGHMeta;

/**
 * 元数据类
 * Class Meta
 * @package common\models\mssgh\extensions;
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
class Meta
{
    /**
     * 集团ID
     * @var
     */
    protected $_g_id;

    /**
     * 酒店ID
     * @var
     */
    protected $_h_id;

    /**
     * 构造函数
     * @param $group_id
     * @param $hotel_id
     */
    public function __construct($group_id, $hotel_id)
    {
        $this->_g_id = $group_id;
        $this->_h_id = $hotel_id;
    }

    /**
     * 获取元数据
     * @param $key
     * @return null|string
     */
    public function getMetaData($key)
    {
        $meta_ar = MssghMeta::findOne(['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id,'meta_key'=>$key]);
        if(empty($meta_ar)) return null;
        return $meta_ar->meta_value;
    }

    /**
     * 设置元数据
     * @param $key
     * @param $value
     * @return bool
     */
    public function setMetaData($key, $value)
    {
        $meta_ar = MssghMeta::findOne(['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id,'meta_key'=>$key]);
        if(empty($meta_ar)){
            $meta_ar = new MssGHMeta();
            $meta_ar->g_id = $this->_g_id;
            $meta_ar->h_id = $this->_h_id;
            $meta_ar->meta_key = $key;
        }
        $meta_ar->meta_value = $value;

        return $meta_ar->save();
    }
}