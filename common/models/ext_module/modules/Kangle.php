<?php

namespace common\models\ext_module\modules;

use common\models\ext_module\base\ModuleConst;
use common\models\ext_module\base\Module;
use common\models\ext_module\base\ModuleList;

/**
 * 康乐模块
 * Class Kangle
 * @package common\models\ext_module\modules
 */
class Kangle extends Module
{
    const KTV = 1; //KTV
    const CHESSROOM = 2; //棋牌室
    const PEDICURE = 3; //足浴
    const SPA = 4; //SPA
    const GYM = 5; //健身
    const SWIMMING = 6; //游泳池
    const HOTSPRING = 7; //温泉
    const GOLF = 8; //高尔夫

    const DEFAULT_MENUS = array(
        self::KTV=>['menu_name'=>'KTV'],
        self::CHESSROOM=>['menu_name'=>'棋牌室'],
        self::PEDICURE=>['menu_name'=>'足浴'],
        self::SPA=>['menu_name'=>'SPA'],
        self::KTV=>['menu_name'=>'健身房'],
        self::SWIMMING=>['menu_name'=>'游泳池'],
        self::HOTSPRING=>['menu_name'=>'温泉'],
        self::GOLF=>['menu_name'=>'高尔夫'],
    );

    /**
     * 创建对象
     * @param $id
     * @param $kangle_type
     * @return Module|null
     */
    public static function getInstance($id, $kangle_type=null)
    {
        return parent::getInstance($id, ModuleConst::MODULE_KANGLE,$kangle_type);
    }

    /**
     * 添加康乐项目
     * @param $group_id
     * @param $hotel_id
     * @param $subtype
     * @param $data
     * @return Module|null
     */
    public static function create($group_id, $hotel_id, $subtype, $data)
    {
        return parent::create($group_id, $hotel_id, ModuleConst::MODULE_KANGLE, $subtype, $data);
    }

    /**
     * 获取康乐项目类型列表
     * @param $group_id
     * @param $hotel_id
     * @param $get_blocked
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMenu($group_id, $hotel_id, $get_blocked=false)
    {
        $lister = new ModuleList($group_id, $hotel_id);
        $type_list = $lister->getSubtypes(ModuleConst::MODULE_KANGLE, $get_blocked);
        $menu=array();
        foreach($type_list as $type_info){
            $menu_type = $type_info['subtype'];
            $menu_icon = $type_info['icon'];
            $menu[] = ['menu_type'=>$menu_type, 'menu_name'=>self::DEFAULT_MENUS[$menu_type]['menu_name'], 'menu_icon'=>$menu_icon];
        }

        return $menu;
    }

    /**
     * 获取项目列表
     * @param $group_id
     * @param $hotel_id
     * @param $kangle_type
     * @param $get_blocked
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getList($group_id, $hotel_id, $kangle_type, $get_blocked=false)
    {
        $lister = new ModuleList($group_id, $hotel_id);
        return $lister->getList(ModuleConst::MODULE_KANGLE, $kangle_type, $get_blocked);
    }
}
