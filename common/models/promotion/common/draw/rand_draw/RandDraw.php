<?php

namespace common\models\promotion\common\draw\rand_draw;

/**
 * 抽奖操作类
 * Class RandDraw
 * @package common\models\promotion\draw
 */
class RandDraw //策略
{
    /**
     * 算法对象
     * @var IAlgorithm
     */
    protected $_algorithm;

    /**
     * 构造函数，必须传入抽奖算法
     * @param IAlgorithm $algorithm
     */
    public function __construct(IAlgorithm $algorithm)
    {
        $this->_algorithm = $algorithm;
    }

    /**
     * 设置抽奖算法
     * @param IAlgorithm $algorithm
     */
    public function setAlgorithm(IAlgorithm $algorithm)
    {
        $this->_algorithm = $algorithm;
    }

    /**
     * 进行抽奖
     * @param $data
     * @return null
     */
    public function draw($data)
    {

        if(!isset($data['prizes'])){
            $data['prizes'] = $data;
        }

        if($this->_algorithm!=null){
            return $this->_algorithm->draw($data);
        }else{
            return null;
        }
    }
}
