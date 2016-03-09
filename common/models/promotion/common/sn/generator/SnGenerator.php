<?php

namespace common\models\promotion\common\sn\generator;

/**
 * SN序列号生成器，并强制对SN码进行封装
 * Class SnGenerator
 * @package common\models\promotion\sn\generator
 */
class SnGenerator //策略
{
    /**
     * Sn算法对象
     * @var
     */
    protected $_sn_algorithm;

    /**
     * 构造函数
     * @param IAlgorithm|null $algorithm
     */
    public function __construct(IAlgorithm $algorithm = null)
    {
        if($algorithm == null){
            $this->_sn_algorithm = new Simple();
        }else{
            $this->_sn_algorithm = $algorithm;
        }
    }

    /**
     * 设置SN生成算法
     * @param IAlgorithm $algorithm
     * @return bool
     */
    public function setAlgorithm(IAlgorithm $algorithm)
    {
        $this->_sn_algorithm=$algorithm;
        return true;
    }

    /**
     * 使用设置的算法生成Sn码
     * @param null $data
     * @return null|string
     */
    public function generateSn($data)
    {
        if(empty($this->_sn_algorithm)){
            return null;
        }
        $naked_sn = $this->_sn_algorithm->generateSn($data);
        $sn_type = $this->_sn_algorithm->getInfo('sn_type');

        //强制对sn码进行封装
        $sn = $this->_wrapSn($naked_sn,$sn_type);
        return $sn;
    }

    /**
     * 使用设置的算法生成Sn兑换密码
     * @param null $data
     * @return null
     */
    public function generatePwd($data=null)
    {
        if(empty($this->_sn_algorithm)){
            return null;
        }

        $naked_pwd = $this->_sn_algorithm->generatePwd($data);
        $pwd = md5($naked_pwd);//强制对sn兑换码进行加密
        return $pwd;
    }

    /**
     * 返回算法信息
     * @return mixed
     */
    public function getAlgorithmInfo()
    {
        if(empty($this->_sn_algorithm)){
            return null;
        }
        return $this->_sn_algorithm->getInfo();
    }

    /**
     * 对SN进行封装
     * @param $naked_sn
     * @param $sn_type
     * @return string
     */
    protected static function _wrapSn($naked_sn, $sn_type)
    {
        $algorithm_bit = self::_algorithmType2Bit($sn_type);

        return $algorithm_bit.$naked_sn;
    }

    /**
     * 对SN进行解封装
     * @param $sn
     */
    protected static function _unWrapSn($sn)
    {
        $algorithm_bit = substr($sn,0,3);
        $algorithm_type = self::_algorithmBit2Type($algorithm_bit);

        if($algorithm_type == 33){
            $naked_sn = substr($sn,3);
        }elseif($algorithm_type >= 0 && $algorithm_type <= 35){
            $naked_sn = substr($sn,1);
        }elseif($algorithm_type >= 36 && $algorithm_type <= 99){
            $naked_sn = substr($sn,3);
        }else{
            $naked_sn = substr($sn,3);
        }
        $sn_info['type'] = $algorithm_type;
        $sn_info['naked_sn'] = $naked_sn;
    }

    /**
     * 将Sn算法类型转换成SN码中的算法识别位
     * @param $num
     * @return string
     */
    protected static function _algorithmType2Bit($num)
    {
        if($num < 0 || $num >= 100){
            $algorithm_bit = 'X00';
        }else if($num <= 9){
            $algorithm_bit = strval($num);
        }elseif($num == 33){
            $algorithm_bit = 'X00';
        }elseif($num <= 35){
            $algorithm_bit = chr( ord('A') + ($num - 10));
        }else{
            $algorithm_bit = 'X'.$num;
        }

        return $algorithm_bit;
    }

    /**
     * 将SN算法识别位转化为Sn算法类型
     * @param $bit
     * @return int
     */
    protected static function _algorithmBit2Type($bit)
    {
        $first_bit = substr(strval($bit),0,1);
        $first_bit_ascii = ord($first_bit);

        if($first_bit_ascii >= ord('0')
            && $first_bit_ascii <= ord('9'))
        {
            $type = (int)$first_bit;
        }
        elseif($first_bit_ascii == ord('X'))
        {
            $second_and_third_bit = substr(strval($bit),1,2);
            $ext_value = (int)$second_and_third_bit;
            if($ext_value < 36 || $ext_value > 99){
                $type = 33;
            }else{
                $type = $ext_value;
            }
        }
        elseif($first_bit_ascii >= ord('A')
            && $first_bit_ascii <= ord('Z'))
        {
            $type = 10 + $first_bit_ascii - ord('A');
        }else{
            $type = 33;
        }

        return $type;
    }
}