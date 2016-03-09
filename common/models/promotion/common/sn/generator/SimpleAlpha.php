<?php

namespace common\models\promotion\common\sn\generator;

/**
 * SN码格式 [6+]活动ID后5位 + [5]数字及字母随机数
 * 注：
 * 若单个活动奖项设置超过上百万个，需增加随机数长度
 * Class SimpleAlpha
 * @package common\models\promotion\sn\generator
 */
Class SimpleAlpha implements IAlgorithm
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
            . sprintf("%05d", self::_randStr(5));

        return $sn;
    }

    /**
     * 生成Sn码对应的兑换密码
     * @param null $data
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
        $info['sn_type'] = 2;
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

    /**
     * 生成随机字母+数字字符串
     * @param $length
     * @param null $escape
     * @return string
     */
    protected function _randStr($length, $escape=null)
    {
        $pattern = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if(!empty($escape)){
            $escape_arr = str_split($escape);

            foreach($escape_arr as $ch){
                $pattern = str_replace($ch,'',$pattern);
            }
        }
        $pattern_len = strlen($pattern);

        $randStr = '';
        for ($i = 0; $i < $length; $i++) {
            $randStr .= $pattern{mt_rand(0, $pattern_len-1)};
        }

        return $randStr;
    }
}