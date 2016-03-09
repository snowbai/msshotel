<?php

namespace common\models\promotion\common\draw\rand_draw;

/**
 * Class Rander
 * @package common\models\promotion\draw\rand_draw
 */
class Rander
{
    /**
     * 随机键名生成函数
     * 以数组元素值的大小，按概率返回键名
     * @param $arr, 输入数组，格式为[ '1' => value, '2' => value, ...]
     * @param int $precision, 精度的倒数
     * @return int|null|string, 返回键名
     */
    public static function randKey($arr,$precision=100)
    {
        $total_weight = array_sum($arr) * $precision;

        if($total_weight <1){
            return null;
        }

        $rand_num = mt_rand(1,$total_weight);
        foreach($arr as $key => $num){
            $rand_num -= $num*$precision;
            if($rand_num <= 0){
                return $key;
            }
        }

        return null;
    }
}
