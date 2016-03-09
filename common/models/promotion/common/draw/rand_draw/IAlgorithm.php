<?php

namespace common\models\promotion\common\draw\rand_draw;

/**
 * 抽奖接口类
 * Interface IAlgorithm
 * @package common\models\promotion\draw\rand_draw
 * @author Lyl <inspii@me.com>
 * @version 2.0
 */
interface IAlgorithm
{
    /**
     * 进行抽奖
     * @param $data
     * @return mixed
     */
    public function draw($data);
}