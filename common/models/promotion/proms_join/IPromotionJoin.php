<?php

namespace common\models\promotion\proms_join;

/**
 * 参加活动接口
 * Interface IPromotionJoin
 * @package common\models\promotion\proms_join
 */
interface IPromotionJoin
{
    /**
     * 获取活动对象
     * @param $prom_id
     * @return mixed
     */
    public static function getInstance($prom_id);

    /**
     * 获取活动信息
     * @return mixed
     */
    public function getInfo();
}
