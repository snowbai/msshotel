<?php

namespace common\models\promotion\common\prize;

use common\models\ar\PromotionPrize;
use yii\base\Exception;

/**
 * 奖品对象
 * Class PrizeObj
 * @package common\models\promotion\prize
 */
class PrizeObj
{
    /**
     * 奖项AR
     * @var null|static
     */
    protected $_prize_ar;

    /**
     * 构造函数
     * PrizeObj constructor.
     * @param $prize_ar
     */
    protected function __construct($prize_ar)
    {
        $this->_prize_ar = $prize_ar;
    }

    /**
     * 获取对象
     * @param $prize_id
     * @return PrizeObj|null
     */
    public static function getInstance($prize_id)
    {
        $prize_ar = PromotionPrize::findOne($prize_id);
        if(empty($prize_ar)) return null;
        else return new PrizeObj($prize_ar);
    }

    /**
     * 添加奖项
     * @param $prom_id
     * @param $activity_id
     * @param $data
     * @return PrizeObj|null
     * @throws \Exception
     */
    public static function createPrize($prom_id, $activity_id, $data)
    {
        $prize_ar = new PromotionPrize();
        $prize_ar->setAttributes($data);
        $prize_ar->prom_id = $prom_id;
        $prize_ar->activity_id = $activity_id;
        if ($prize_ar->validate() && $prize_ar->insert()) {
            return new PrizeObj(0, $prize_ar);
        }else{
            return null;
        }
    }

    /**
     * 获取奖项信息
     * @return array|null
     */
    public function getInfo()
    {
        return $this->_prize_ar->toArray();
    }

    /**
     * 修改奖项信息
     * @param $data
     * @return bool
     */
    public function modify($data)
    {
        unset($data['prize_id']);
        unset($data['prom_id']);
        unset($data['activity_id']);
        unset($data['prize_type']);
        unset($data['add_time']);
        $this->_prize_ar->setAttributes($data);

        return $this->_prize_ar->save();
    }

    /**
     * 修改奖品剩余数量
     * @param $ups
     * @return bool
     */
    public function updateRemains($ups)
    {
        try{
            return $this->_prize_ar->updateCounters(['remain_num'=>$ups]);
        }catch(Exception $e){
            return false;
        }
    }
}