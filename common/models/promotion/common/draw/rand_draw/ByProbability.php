<?php

namespace common\models\promotion\common\draw\rand_draw;

/**
 * 按概率进行抽奖（需提供抽不到奖的概率）
 * Class ByProbability
 * @package common\models\promotion\draw_machines\rand_draw
 */
Class ByProbability implements IAlgorithm
{
    /**
     * @param $data,格式为['prizes'=>[['id'=>value, 'probability'=>value, 'remain_num'=>value],...]]
     * @return int|string
     */
    public function draw($data)
    {
        if(!is_array($data)) return null;
        $prizes = isset($data['prizes']) ? $data['prizes'] : $data;

        $probability_arr = array();
        foreach ($prizes as $prize){
            $prize_id = $prize['id'];
            $probability = $prize['point'];
            $remain_num = $prize['remain_num'];
            if($remain_num>0){
                $probability_arr[$prize_id] = $probability;
            }
        }

        return Rander::randKey($probability_arr,10000);
    }
}