<?php

namespace common\models\order\orders\order_no;

/**
 * 订单号生成器，并强制对订单号进行封装
 * Class OrderNoGenerator
 * @package common\models\promotion\sn\generator
 * @author Lyl <inspii@me.com>
 * @version 2.0
 */
class OrderNoGenerator //策略
{
    /**
     * Sn算法对象
     * @var
     */
    protected $_algorithm;

    /**
     * 构造函数
     * @param IOrderNo|null $algorithm
     */
    public function __construct(IOrderNo $algorithm = null)
    {
        if($algorithm == null){
            $this->_algorithm = new Simple();
        }else{
            $this->_algorithm = $algorithm;
        }
    }

    /**
     * 设置生成算法
     * @param IOrderNo $algorithm
     * @return bool
     */
    public function setAlgorithm(IOrderNo $algorithm)
    {
        $this->_algorithm=$algorithm;
        return true;
    }

    /**
     * 使用设置的算法生成订单号
     * @param null $data
     * @return null|string
     */
    public function generateNo($data)
    {
        if(empty($this->_algorithm)){
            return null;
        }
        $sn = $this->_algorithm->generate($data);

        return $sn;
    }

    /**
     * 返回算法信息
     * @return mixed
     */
    public function getAlgorithmInfo()
    {
        if(empty($this->_algorithm)){
            return null;
        }
        return $this->_algorithm->getInfo();
    }
}