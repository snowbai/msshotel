<?php

namespace common\models\ext_module\base;

use yii\base\Exception;
use common\models\ar\ExtModule as ExtModuleAR;
use common\models\base\Constant;

/**
 * 扩展模块类
 * Class Module
 * @package common\models\ext_module\base
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
class Module
{
    /**
     * @var ExtModuleAR
     */
    protected $_module_ar;

    /**
     * 构造函数
     * ExtModule constructor.
     * @param null $module_ar
     */
    protected function __construct($module_ar)
    {
        $this->_module_ar = $module_ar;
    }

    /**
     * 创建对象
     * @param $id
     * @param null $type
     * @param null $subtype
     * @return Module|null
     */
    public static function getInstance($id, $type=null, $subtype=null)
    {
        $query = ExtModuleAR::find()->where(['id'=>$id]);
        if($type!==null){
            if(is_array($type)&&count($type)>2){
                $query->andWhere(['between','type',$type[0],$type[1]]);
            }elseif(is_int($type)){
                $query->andWhere(['type'=>$type]);
            }
        }
        if($subtype!==null){
            $query->andWhere(['subtype'=>$subtype]);
        }
        $module_ar = $query->one();

        if(empty($module_ar)) return null;
        return new Module($module_ar);
    }

    /**
     * 添加项目
     * @param $group_id
     * @param $hotel_id
     * @param $type
     * @param $subtype
     * @param $data
     * @return Module|null
     */
    public static function create($group_id, $hotel_id, $type, $subtype, $data)
    {
        $module_ar = new ExtModuleAR();
        $module_ar->setAttributes($data);
        $module_ar->g_id = $group_id;
        $module_ar->h_id = $hotel_id;
        $module_ar->type = $type;
        $module_ar->subtype = $subtype;
        $module_ar->list_order = self::_getNewListOrder($group_id, $hotel_id, $type, $subtype);
        if($module_ar->save()){
            return new Module($module_ar);
        }else{
            return null;
        }
    }

    /**
     * 获取信息
     * @return array|null
     */
    public function getInfo()
    {
        if(empty($this->_module_ar)) return null;
        return $this->_module_ar->toArray();
    }

    /**
     * 更新信息
     * @param $data
     * @return bool
     */
    public function update($data)
    {
        if(empty($this->_module_ar)) return false;

        unset($data['id']);
        unset($data['type']);
        unset($data['subtype']);
        unset($data['g_id']);
        unset($data['h_id']);
        unset($data['status']);
        unset($data['add_time']);

        $this->_module_ar->setAttributes($data);
        return $this->_module_ar->save();
    }

    /**
     * 删除
     * @param $soft
     * @return bool
     */
    public function delete($soft=false)
    {
        if(empty($this->_module_ar)) return false;

        if($soft){
            $this->_module_ar->status = Constant::STATUS_DELETED;
            return $this->_module_ar->save();
        }else{
            return $this->_module_ar->delete();
        }
    }

    /**
     * 屏蔽
     * @return bool
     */
    public function block()
    {
        if(empty($this->_module_ar)) return false;
        $this->_module_ar->status = Constant::STATUS_BLOCKED;
        return $this->_module_ar->save();
    }

    /**
     * 取消屏蔽
     * @return bool
     */
    public function unblock()
    {
        if(empty($this->_module_ar)) return false;
        $this->_module_ar->status = 0;
        return $this->_module_ar->save();
    }

    /**
     * 上移
     * @param int $num
     * @return bool
     */
    public function moveUp($num=1)
    {
        if($num==0) return true;
        elseif($num<0) return false;
        if(empty($this->_module_ar)) return false;

        $group_id = $this->_module_ar->g_id;
        $hotel_id = $this->_module_ar->h_id;
        $type = $this->_module_ar->type;
        $subtype = $this->_module_ar->subtype;
        $cur_order = $this->_module_ar->list_order;
        $new_order = $cur_order + $num;
        try{
            $success = ExtModuleAR::updateAllCounters(['list_order'=>-1],
                ['AND', ['g_id'=>$group_id, 'h_id'=>$hotel_id, 'type'=>$type, 'subtype'=>$subtype], "list_order>$cur_order AND list_order<=$new_order"]);
            if($success){
                $this->_module_ar->list_order = $new_order;
                return $this->_module_ar->save();
            }else{
                return false;
            }
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * 下移
     * @param int $num
     * @return bool
     */
    public function moveDown($num=1)
    {
        if($num==0) return true;
        elseif($num<0) return false;
        if(empty($this->_module_ar)) return false;

        $group_id = $this->_module_ar->g_id;
        $hotel_id = $this->_module_ar->h_id;
        $type = $this->_module_ar->type;
        $subtype = $this->_module_ar->subtype;
        $cur_order = $this->_module_ar->list_order;
        $new_order = max(0, $cur_order - $num);
        try{
            $success = ExtModuleAR::updateAllCounters(['list_order'=>+1],
                ['AND', ['g_id'=>$group_id, 'h_id'=>$hotel_id, 'type'=>$type, 'subtype'=>$subtype], "list_order>=$new_order AND list_order<$cur_order"]);
            if($success){
                $this->_module_ar->list_order = $new_order;
                return $this->_module_ar->save();
            }else{
                echo 'aaa';
                return false;
            }
        }catch(Exception $e){
            echo 'bb';
            return false;
        }
    }

    /**
     * 获取新的排序值
     * @param $group_id
     * @param $hotel_id
     * @param $type
     * @param $subtype
     * @return mixed
     */
    protected static function _getNewListOrder($group_id, $hotel_id, $type, $subtype)
    {
        $new_list_order = ExtModuleAR::find()->where(['g_id'=>$group_id, 'h_id'=>$hotel_id, 'type'=>$type, 'subtype'=>$subtype])->max('list_order');

        return $new_list_order+1;
    }
}
