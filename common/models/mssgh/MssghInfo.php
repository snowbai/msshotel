<?php

namespace common\models\mssgh;

use common\models\ar\MssGH;

/**
 * 酒店集团信息类
 * Class MssghInfo
 * @package common\models\hotel
 */
class MssghInfo
{
    /**
     * @var MssGH
     */
    protected $_mssgh_ar;

    /**
     * 构造函数
     * Info constructor.
     * @param $mssgh_ar
     */
    protected function __construct($mssgh_ar)
    {
        $this->_mssgh_ar = $mssgh_ar;
    }

    /**
     * 获取对象
     * @param $group_id
     * @param $hotel_id
     * @return MssghInfo|null
     */
    public static function getInstance($group_id, $hotel_id)
    {
        $mssgh_ar = MssGH::findOne(['g_id'=>$group_id, 'h_id'=>$hotel_id]);
        if(empty($mssgh_ar)) return null;
        return new MssghInfo($mssgh_ar);
    }

    /**
     * 获取集团或酒店信息
     * @return array|null
     */
    public function getInfo()
    {
        if(empty($this->_mssgh_ar)) return null;
        return $this->_mssgh_ar->toArray();
    }

    /**
     * 设置集团或酒店酒店信息
     * @param $data
     * @return bool
     */
    public function setInfo($data)
    {
        if(empty($this->_mssgh_ar)) return false;

        unset($data['h_id']);
        unset($data['g_id']);
        unset($data['add_time']);
        unset($data['status']);
        $this->_mssgh_ar->setAttributes($data);

        return $this->_mssgh_ar->save();
    }
}
