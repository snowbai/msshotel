<?php

namespace common\models\promotion\proms_join;

use common\models\base\Constant;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivitySeckill as SeckillAR;
use common\models\promotion\proms_join\common\DoCheck;

/**
 * 促销活动参与类
 * Class SeckillJoin
 * @package common\models\promotion\proms_join
 */
class SeckillJoin implements IPromotionJoin
{
    /**
     * 使用公用方法
     */
    use DoCheck;

    /**
     * 活动ID
     * @var PromotionAR
     */
    protected $_prom_ar;

    /**
     * 具体活动AR
     * @var SeckillAR
     */
    protected $_act_ars;

    /**
     * 构造函数
     * SeckillJoin constructor.
     * @param $prom_ar
     * @param $act_ars
     */
    protected function __construct($prom_ar, $act_ars)
    {
        $this->_prom_ar = $prom_ar;
        $this->_act_ars = $act_ars;
    }

    public static function getInstance($prom_id)
    {
        $prom_ar = PromotionAR::findOne((int)$prom_id);
        if(empty($prom_ar)) return null;
        $result = SeckillAR::find()
            ->where(['prom_id'=>$prom_id])
            ->andWhere(['<>','status',Constant::STATUS_DELETED])
            ->orderBy('start_time ASC')->all();
        if(empty($act_ars)) return null;
        $act_ars = array();
        foreach($result as $ar){
            $act_ars[$ar->activity_id] = $ar;
        }

        return new SeckillJoin($prom_ar, $act_ars);
    }

    public function getInfo()
    {
        $info['promotion'] = $this->_prom_ar->toArray();
        foreach($this->_act_ars as $key=>$ar){
            $info['activities'][$key] = $ar->toArray();
        }

        return $info;
    }

    /**
     * 判断活动能否参加
     * @return bool
     */
    public function checkValid()
    {
        return $this->_checkPromValid($this->_prom_ar);
    }

    /**
     * 获取秒杀场次信息
     * @return array
     */
    public function getSessions()
    {
        $detail = $this->getInfo();
        $info = $detail['promotion'];
        $list = $detail['activities'];
        $on_going_key = 0;
        $next_key = 0;
        $last_key = 0;
        foreach($list as $key=>$item){
            $now = time();
            $s = strtotime($item['start_time']);
            $e = strtotime($item['end_time']);
            if($now > $e){
                $list[$key]['status'] = '已结束';
            }elseif($now < $s){
                $next_key = $next_key==0 ? $key : $next_key; //取第一个尚未开始
                $list[$key]['status'] = '尚未开始';
            }else{
                $on_going_key = $key;
                if($item['remain_num']>0){
                    $list[$key]['status'] = '进行中';
                }else{
                    $list[$key]['status'] = '已抢光';
                }
            }
            $last_key = $key;
        }
        if($next_key==0) $next_key=$last_key;
        $on_going = isset($list[$on_going_key]) ? $list[$on_going_key] : array();
        $next = isset($list[$next_key]) ? $list[$next_key] : array();

        return ['on_going'=>$on_going, 'next'=>$next, 'sessions'=>$list, 'info'=>$info];
    }
}