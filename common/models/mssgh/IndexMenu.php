<?php

namespace common\models\mssgh;

use yii\base\Exception;
use common\models\ar\MssGHMenu as MenuAR;
use common\models\base\Constant;

/**
 * 导航菜单管理类
 * Class IndexMenu
 * @package common\models\mssgh
 */
class IndexMenu
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
     * 获取餐单列表
     * @param bool|false $get_hide
     * @return array|null
     */
    public function getMenu($get_hide=false)
    {
        $menu_query = MenuAR::find()->select('menu_id,menu_type,menu_name,menu_url,parent_id,list_order')
            ->where(['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id])
            ->andWhere(['<>','status',Constant::STATUS_DELETED]);

        if(!$get_hide){
            $menu_query->andWhere(['<>','status',Constant::STATUS_BLOCKED]);
        }

        $menu_list = $menu_query->orderBy('list_order ASC')->asArray()->all();

        return $menu_list;
    }

    /**
     * 获取树形菜单列表
     * @param bool|false $get_hide
     * @param int $parent_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getMenuTree($parent_id=0, $get_hide=false)
    {
        $menu_list = $this->getMenu($get_hide);

        if($parent_id == 0){//如果parent_id为0，则先添加根节点到列表中
            $menu_list[] = ['menu_id'=>0,'parent_id'=>null,'menu_name'=>'root','child'=>[]];
        }
        $menu_tree = $this->_getTree($parent_id,$menu_list);
        if($parent_id == 0){//如果parent_id为0，则去除先前添加的根节点
            $menu_tree = $menu_tree['child'];
        }

        return $menu_tree;
    }

    /**
     * 获取单层菜单列表（或递归获取多层）
     * 注意：使用递归，会造成数据库查询次数较多，影响效率，
     * 建议使用getMenuTree获取出所有菜单，然后再进行计算父子关系。
     * @param int $parent_id
     * @param bool|false $get_hide
     * @param bool $recursive
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getChildMenu($parent_id=0, $get_hide=false, $recursive=false)
    {
        $menu_query = MenuAR::find()->select('menu_id,menu_type,menu_name,menu_url,list_order')
            ->where(['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id, 'parent_id'=>$parent_id])
            ->andWhere(['<>','status',Constant::STATUS_DELETED]);

        if(!$get_hide){
            $menu_query->andWhere(['<>','status',Constant::STATUS_BLOCKED]);
        }

        $menu_list = $menu_query->orderBy('list_order ASC')->asArray()->all();

        if($recursive){
            foreach($menu_list as $k=>$v){
                $menu_list[$k]['child'] = $this->getChildMenu($v['menu_id'],$get_hide,true);
            }
        }

        return $menu_list;
    }

    /**
     * 添加菜单
     * @param $parent_id
     * @param $menu_type
     * @param $menu_name
     * @param $menu_url
     * @param $menu_img
     * @return bool
     */
    public function addMenu($parent_id, $menu_type, $menu_name, $menu_url='', $menu_img='')
    {
        $list_order = $this->_getNewListOrder($parent_id);

        $menu_obj = new MenuAR();
        $menu_obj->g_id =$this->_g_id;
        $menu_obj->h_id = $this->_h_id;
        $menu_obj->parent_id = $parent_id;
        $menu_obj->menu_type = $menu_type;
        $menu_obj->menu_name = $menu_name;
        $menu_obj->list_order = $list_order;
        $menu_obj->menu_url = $menu_url;
        $menu_obj->menu_img = $menu_img;

        return $menu_obj->save();
    }

    /**
     * 编辑菜单
     * @param $menu_id
     * @param $menu_name
     * @param $menu_url
     * @param $menu_img
     * @return bool
     */
    public function editMenu($menu_id, $menu_name, $menu_url=null, $menu_img=null)
    {
        $menu_obj = MenuAR::findOne(['menu_id'=>$menu_id, 'g_id'=>$this->_g_id, 'h_id'=>$this->_h_id]);
        if(empty($menu_obj)) return false;

        $menu_obj->menu_name = $menu_name;
        ($menu_url!==null) && $menu_obj->menu_url = $menu_url;
        ($menu_img!==null) && $menu_obj->menu_img = $menu_img;

        return $menu_obj->save();
    }

    /**
     * 删除菜单
     * @param $menu_id
     * @param $soft
     * @return bool
     */
    public function deleteMenu($menu_id, $soft=false)
    {
        $menu_obj = MenuAR::findOne(['menu_id'=>$menu_id, 'g_id'=>$this->_g_id, 'h_id'=>$this->_h_id]);
        if(empty($menu_obj)) return false;

        if($soft){
            $menu_obj->status = Constant::STATUS_DELETED;
            return $menu_obj->save();
        }else{
            return $menu_obj->delete();
        }

    }

    public function deleteMenuByType($menu_type, $soft=false)
    {
        $menu_obj = MenuAR::findOne(['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id, 'menu_type'=>$menu_type]);
        if(empty($menu_obj)) return false;

        if($soft){
            $menu_obj->status = Constant::STATUS_DELETED;
            return $menu_obj->save();
        }else{
            return $menu_obj->delete();
        }
    }

    /**
     * 隐藏菜单
     * @param $menu_id
     * @return bool
     */
    public function hideMenu($menu_id)
    {
        $menu_obj = MenuAR::findOne(['menu_id'=>$menu_id, 'g_id'=>$this->_g_id, 'h_id'=>$this->_h_id]);
        if(empty($menu_obj)) return false;

        $menu_obj->status = Constant::STATUS_BLOCKED;

        return $menu_obj->save();
    }

    /**
     * 显示菜单
     * @param $menu_id
     * @return bool
     */
    public function showMenu($menu_id)
    {
        $menu_obj = MenuAR::findOne(['menu_id'=>$menu_id, 'g_id'=>$this->_g_id, 'h_id'=>$this->_h_id]);
        if(empty($menu_obj)) return false;

        $menu_obj->status = Constant::STATUS_NORMAL;

        return $menu_obj->save();
    }

    /**
     * 修改菜单层级
     * @param $menu_id
     * @param $new_parent_id
     * @return bool
     */
    public function changeParent($menu_id, $new_parent_id)
    {
        $menu_obj = MenuAR::findOne(['menu_id'=>$menu_id, 'g_id'=>$this->_g_id, 'h_id'=>$this->_h_id]);
        if(empty($menu_obj)) return false;

        $list_order = $this->_getNewListOrder($new_parent_id);
        $menu_obj->parent_id = $new_parent_id;
        $menu_obj->list_order = $list_order;

        return $menu_obj->save();
    }

    /**
     * 上移菜单项
     * @param $menu_id
     * @param int $num
     * @return bool
     */
    public function moveUp($menu_id, $num=1)
    {
        if($num==0) return true;
        elseif($num<0) return false;
        $menu_obj = MenuAR::findOne(['menu_id'=>$menu_id, 'g_id'=>$this->_g_id, 'h_id'=>$this->_h_id]);
        if(empty($menu_obj)) return false;//没有菜单可以移动

        $parent_id = $menu_obj->parent_id;
        $cur_order = $menu_obj->list_order;
        $new_order = $cur_order + $num;
        try{
            $success = MenuAR::updateAllCounters(['list_order'=>-1],
                ['AND', ['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id, 'parent_id'=>$parent_id], "list_order>$cur_order AND list_order<=$new_order"]);
            if($success){
                $menu_obj->list_order = $new_order;
                return $menu_obj->save();
            }else{
                return false;
            }
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * 下移菜单项
     * @param $menu_id
     * @param int $num
     * @return bool
     */
    public function moveDown($menu_id, $num=1)
    {
        if($num==0) return true;
        elseif($num<0) return false;
        $menu_obj = MenuAR::findOne(['menu_id'=>$menu_id, 'g_id'=>$this->_g_id, 'h_id'=>$this->_h_id]);
        if(empty($menu_obj)) return false;//没有菜单可以移动

        $parent_id = $menu_obj->parent_id;
        $cur_order = $menu_obj->list_order;
        $new_order = $cur_order - $num;
        try{
            $success = MenuAR::updateAllCounters(['list_order'=>+1],
                ['AND', ['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id, 'parent_id'=>$parent_id], "list_order>=$new_order AND list_order<$cur_order"]);
            if($success){
                $menu_obj->list_order = $new_order;
                return $menu_obj->save();
            }else{
                return false;
            }
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * 获取下一个排序值（用于添加新菜单）
     * @param $parent_id
     * @return int|mixed
     */
    private function _getNewListOrder($parent_id)
    {
        $max_list_order = MenuAR::find()->where(['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id,'parent_id'=>$parent_id])->max('list_order');
        if($max_list_order===null){
            $new_list_order = 0;
        }else{
            $new_list_order = $max_list_order + 1;
        }

        return $new_list_order;
    }

    /**
     * 转换成树形结构
     * @param $root_id
     * @param $list
     * @return array|null
     */
    private static function _getTree($root_id, $list)
    {
        $root = array();
        $children = array();

        foreach($list as $k => $item){
            if($item['menu_id'] == $root_id){
                $root = $item;
                unset($list[$k]); //防止死循环及用于提高效率
            }elseif($item['parent_id'] == $root_id){
                $children[] = self::_getTree($item['menu_id'],$list);
            }
        }

        if(!empty($root)){
            $root['child'] = $children;
            return $root;
        }else{
            return null;
        }
    }
}
