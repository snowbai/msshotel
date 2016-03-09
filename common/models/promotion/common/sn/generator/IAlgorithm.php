<?php

namespace common\models\promotion\common\sn\generator;

/**
 * SN码生成算法接口
 * 实现该类的SN码生成器并不会保证生成的SN码唯一
 * 唯一性需自行判断
 * Interface IAlgorithm
 * @package common\models\promotion\sn\generator
 * @author Lyl <inspii@me.com>
 * @version 2.0
 */
interface IAlgorithm
{
    /**
     * 生成一个SN序列号
     * @param $data
     * @return mixed
     */
    public static function generateSn($data);

    /**
     * 生成密码
     * @param $data
     * @return mixed
     */
    public static function generatePwd($data);

    /**
     * 获取SN算法信息
     * @param $field
     * @return mixed
     */
    public static function getInfo($field='');
}