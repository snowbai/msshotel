<?php

namespace common\models\promotion\common\sn\generator;

/**
 * SN码格式 [6+]活动ID + [5]数字随机数
 * 注：
 * 若单个活动奖项设置超过5万个，需增加随机数长度
 * Class Simple
 * @package common\models\promotion\sn\generator
 */
Class Simple implements IAlgorithm
{
    /**
     * 生成SN码
     * @param $data
     * @return string
     */
    public static function generateSn($data)
    {
        if(is_numeric($data)){
            $prom_id = (int)$data;
        }else{
            $prom_id = isset($data['prom_id']) ? (int)$data['prom_id'] : 0;
        }

        $sn = sprintf("%06d", $prom_id) //长度为6位或以上
            . sprintf("%05d", mt_rand(0,99999));

        return $sn;
    }

    /**
     * 生成SN相应的兑换密码
     * @param $data
     * @return string
     */
    public static function generatePwd($data)
    {
        return sprintf('%06d',rand(0,999999));
    }

    /**
     * 获取算法信息
     * @param string $field
     * @return int|string
     */
    public static function getInfo($field='')
    {
        $info['sn_type'] = 1;
        $info['version'] = '1.0';
        $info['about'] = '';

        switch($field){
            case 'sn_type':
                return $info['sn_type'];
            case 'version':
                return $info['version'];
            case 'about':
                return $info['about'];
            default:
                return $info;
        }
    }
}