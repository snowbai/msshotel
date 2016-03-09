<?php

namespace common\models\ext_module\modules;

use common\models\ar\ExtModule as ExtModuleAR;
use common\models\mssgh\IndexMenu;
use common\models\ext_module\base\ModuleConst;
use common\models\ext_module\base\Module;
use common\models\ext_module\base\ModuleList;

/**
 * 自定义模块
 * Class Custom
 * @package common\models\ext_module\modules
 */
class Custom extends Module
{
    /**
     * 创建对象
     * @param $id
     * @param null $type
     * @param null $subtype
     * @return Module|null
     */
    public static function getInstance($id, $type=null, $subtype=null)
    {
        $module_obj = parent::getInstance($id, $type, $subtype);
        if(empty($module_obj)) return null;
        $module_type = $module_obj->getInfo()['type'];
        if($module_type < ModuleConst::MODULE_CUSTOM[0] || $module_type > ModuleConst::MODULE_CUSTOM[1]) return null;
        return $module_obj;
    }

    /**
     * 创建模块
     * @param $group_id
     * @param $hotel_id
     * @param $data
     * @return Module|null
     */
    public static function create($group_id, $hotel_id, $data)
    {
        $type = self::_getNewType($group_id, $hotel_id);
        $module_obj = parent::create($group_id, $hotel_id, $type, 0, $data);
        if(!empty($module_obj)){
            $info = $module_obj->getInfo();
            $menu_name = $info['name'];
            $menu_img = !empty($info['icon']) ? $info['icon'] : 'custom.jpg';
            $menu_url = 'custom/index?id='.$info['id'];
            self::_addMenu($group_id, $hotel_id, $type, $menu_name, $menu_url, $menu_img);
            return true;
        }else{
            return false;
        }
    }

    /**
     * 删除模块
     * @return bool
     */
    public function delete()
    {
        if(empty($this->_module_ar)) return false;
        $group_id = $this->_module_ar->g_id;
        $hotel_id = $this->_module_ar->h_id;
        $menu_type = $this->_module_ar->type;
        if(parent::delete(false)){
            $this->_delMenu($group_id, $hotel_id, $menu_type);
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取项目列表
     * @param $group_id
     * @param $hotel_id
     * @param $type
     * @param $show_block
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getList($group_id, $hotel_id, $type, $show_block=false)
    {
        $lister = new ModuleList($group_id, $hotel_id);
        return $lister->getList($type, 0, $show_block);
    }

    /**
     * 生成新模块类型值
     * @param $group_id
     * @param $hotel_id
     * @return int
     */
    protected static function _getNewType($group_id, $hotel_id)
    {
        $result = ExtModuleAR::find()->select('type')->where(['g_id'=>$group_id, 'h_id'=>$hotel_id])
            ->asArray()->all();
        $types = array();
        foreach($result as $item){
            $types[] = $item['type'];
        }

        do{
            $new_type = rand(ModuleConst::MODULE_CUSTOM[0], ModuleConst::MODULE_CUSTOM[1]);
        }while(in_array($new_type, $types));

        return $new_type;
    }

    /**
     * 添加菜单
     * @param $group_id
     * @param $hotel_id
     * @param $type
     * @param $menu_name
     * @param $menu_url
     * @param $menu_img
     * @return bool
     */
    protected static function _addMenu($group_id, $hotel_id, $type, $menu_name, $menu_url='', $menu_img='')
    {
        $menu_obj = new IndexMenu($group_id, $hotel_id);
        return $menu_obj->addMenu(0, $type, $menu_name, $menu_url, $menu_img);
    }

    /**
     * 删除菜单
     * @param $group_id
     * @param $hotel_id
     * @param $type
     * @return bool|false|int
     */
    protected static function _delMenu($group_id, $hotel_id, $type)
    {
        $menu_obj = new IndexMenu($group_id, $hotel_id);
        return $menu_obj->deleteMenuByType($type, false);
    }
}
