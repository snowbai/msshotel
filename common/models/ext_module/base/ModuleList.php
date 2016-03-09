<?php

namespace common\models\ext_module\base;

use common\models\ar\ExtModule as ExtModuleAR;
use common\models\base\Constant;

/**
 * 自定义模块列表类
 * Class ModuleList
 * @package common\models\custom
 */
class ModuleList
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
     * ModuleList constructor.
     * @param $group_id
     * @param $hotel_id
     */
    public function __construct($group_id, $hotel_id)
    {
        $this->_g_id = $group_id;
        $this->_h_id = $hotel_id;
    }

    /**
     * 获取类型列表
     * @param null $is_sys
     * @param $get_blocked
     * @return array
     */
    public function getTypes($is_sys=null, $get_blocked=false)
    {
        $query = ExtModuleAR::find()->select('type, icon')->where(['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id]);
        if($is_sys===true){
            $query->andWhere('type<100');
        }elseif($is_sys===false){
            $query->andWhere('type>=100');
        }
        if($get_blocked){
            $query->andWhere(['<>', 'status', Constant::STATUS_DELETED]);
        }else{
            $query->andWhere(['IN', 'status', [Constant::STATUS_DELETED,Constant::STATUS_BLOCKED]]);
        }

        return $query->distinct()->groupBy('type')->asArray()->all();
    }

    /**
     * 获取子类型列表
     * @param $type
     * @param $get_blocked
     * @return array
     */
    public function getSubtypes($type, $get_blocked=false)
    {
        $query = ExtModuleAR::find()->select('subtype, icon')
            ->where(['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id, 'type'=>$type]);
        if($get_blocked){
            $query->andWhere(['<>', 'status', Constant::STATUS_DELETED]);
        }else{
            $query->andWhere(['NOT IN', 'status', [Constant::STATUS_DELETED,Constant::STATUS_BLOCKED]]);
        }
        return $query->distinct()->groupBy('subtype')->asArray()->all();
    }

    /**
     * 获取列表
     * @param $type
     * @param $subtype
     * @param $get_blocked
     * @return array
     */
    public function getList($type, $subtype, $get_blocked=false)
    {
        $query = ExtModuleAR::find()->select('id, name, brief')->where(['g_id'=>$this->_g_id, 'h_id'=>$this->_h_id]);
        if($type!==null){
            $query->andWhere(['type'=>$type]);
        }
        if($subtype!==null){
            $query->andWhere(['subtype'=>$subtype]);
        }
        if($get_blocked){
            $query->andWhere(['<>', 'status', Constant::STATUS_DELETED]);
        }else{
            $query->andWhere(['NOT IN', 'status', [Constant::STATUS_DELETED,Constant::STATUS_BLOCKED]]);
        }
        return $query->orderBy('list_order DESC')->asArray()->all();
    }
}
