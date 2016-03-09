<?php

namespace common\models\base;

/**
 * Class Constant
 * @package common\models\base
 */
class Constant
{
    /*
     * 通用状态值
     */
    const STATUS_NORMAL = 0;                //普通正常状态
    const STATUS_RECOMMENDED = 1;           //已推荐状态
    const STATUS_DELETED = 10;              //已删除状态
    const STATUS_BLOCKED = 11;               //屏蔽状态
    const STATUS_DISABLED = 12;             //失效状态
    const STATUS_DEACTIVATED = 13;          //未激活状态
    const STATUS_OCCUPIED = 14;             //占用状态
    const STATUS_EXPIRED = 15;              //过期状态

}
