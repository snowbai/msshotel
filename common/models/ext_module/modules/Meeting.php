<?php

namespace common\models\ext_module\modules;

use common\models\ext_module\base\ModuleConst;
use common\models\ext_module\base\Module;
use common\models\ext_module\base\ModuleList;

/**
 * 会议模块
 * Class Meeting
 * @package common\models\ext_module\modules
 */
class Meeting extends Module
{
    /**
     * 创建对象
     * @param $id
     * @return Module|null
     */
    public static function getInstance($id)
    {
        return parent::getInstance($id, ModuleConst::MODULE_MEETING, null);
    }

    /**
     * 添加项目
     * @param $group_id
     * @param $hotel_id
     * @param $data
     * @return Module|null
     */
    public static function create($group_id, $hotel_id, $data)
    {
        return parent::create($group_id, $hotel_id, ModuleConst::MODULE_MEETING, 0, $data);
    }

    /**
     * 获取项目列表
     * @param $group_id
     * @param $hotel_id
     * @param $get_blocked
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getList($group_id, $hotel_id, $get_blocked=false)
    {
        $lister = new ModuleList($group_id, $hotel_id);
        return $lister->getList(ModuleConst::MODULE_MEETING, 0, $get_blocked);
    }
}
