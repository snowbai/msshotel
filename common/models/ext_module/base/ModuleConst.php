<?php

namespace common\models\ext_module\base;

/**
 * Class ModuleConst
 * @package common\models\ext_module\base
 */
class ModuleConst
{
    /**
     * 模块类型
     * 0-100 系统，100+用户自定义
     */
    const MODULE_KANGLE = 1; //康乐
    const MODULE_BANQUET = 2; //宴会
    const MODULE_MEETING = 3; //会议
    const MODULE_CUSTOM = [100,999]; //自定义模块

}
