<?php

namespace common\models\promotion\proms_join\common;

use common\models\base\Constant;

/**
 * 检查合法性
 * Class DoCheck
 * @package common\models\promotion\proms_join\common
 */
trait DoCheck
{
    /**
     * 判断活动是否合法
     * @param $prom_ar
     * @return bool
     */
    protected  function _checkPromValid($prom_ar)
    {
        if(empty($prom_ar)) return false;

        $invalid_status = [Constant::STATUS_BLOCKED, Constant::STATUS_DELETED, Constant::STATUS_DISABLED];
        if(in_array($prom_ar->status, $invalid_status)) return false;

        $now = time();
        $prom_start = strtotime($prom_ar->start_time);
        $prom_end = strtotime($prom_ar->end_time);
        if($now<$prom_start || $now>$prom_end) return false;

        return true;
    }
}