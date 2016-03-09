<?php

namespace common\models\promotion\common\log;

use common\models\ar\PromotionShareLog;
use yii\base\Exception;

/**
 * 促销活动分享日志类
 * Class ShareLogger
 * @package common\models\promotion\common\log
 */
class ShareLogger
{
    /**
     * 活动ID
     * @var
     */
    protected $_prom_id;

    /**
     * 子活动ID
     * @var int
     */
    protected $_activity_id;

    /**
     * 构造函数
     * @param $prom_id
     * @param int $activity_id
     */
    public function __construct($prom_id,$activity_id)
    {
        $this->_prom_id = (int)$prom_id;
        $this->_activity_id = (int)$activity_id;
    }

    /**
     * 记录用户享
     * @param $member_id
     * @param $data
     * @param string $member_type
     * @return bool|int
     * @throws \Exception
     */
    public function log($member_id, $data, $member_type='member_id')
    {
        $query = PromotionShareLog::find()->where(['prom_id'=>$this->_prom_id,'activity_id'=>$this->_activity_id]);
        switch($member_type){
            case 'member_id':
            case 'open_id':
            case 'phone':
                $query->andWhere([$member_type => $member_id]);
                break;
            default:
                $query->andWhere(['exch_type'=>(int)$member_type,'exch_value' => $member_id]);
        }
        $query->andWhere(['>=', 'add_time', date('Y-m-d')]);
        $query->andWhere(['<', 'add_time', date('Y-m-d',strtotime('+1 day'))]);
        $share_log_ar = $query->one();

        if(!empty($share_log_ar)){
            try{
                $share_log_ar->updateCounters(['share_cnt'=>+1]);
                return $share_log_ar->log_id;
            }catch(Exception $e){
                return false;
            }
        }else{
            $share_log_ar = new PromotionShareLog();
            $share_log_ar->setAttributes($data);
            $share_log_ar->prom_id = $this->_prom_id;
            $share_log_ar->activity_id = $this->_activity_id;
            $share_log_ar->share_cnt = 1;

            if ($share_log_ar->validate() && $share_log_ar->insert() ) {
                return $share_log_ar->log_id;
            }else{
                return false;
            }
        }
    }

    /**
     * 获取本活动被分享次数
     * @param null $start_time
     * @param null $end_time
     * @return int|string
     */
    public function getShareTimes($start_time=null, $end_time=null)
    {
        return self::getShareTimesEx($this->_prom_id, $this->_activity_id, $start_time, $end_time);
    }

    /**
     * 获取会员分享本活动的次数
     * @param $member_id
     * @param string $member_type
     * @param null $start_time
     * @param null $end_time
     * @return int|string
     */
    public function getMemShareTimes($member_id, $member_type='member_id', $start_time=null, $end_time=null)
    {
        return self::getMemShareTimesEx($this->_prom_id, $this->_activity_id, $member_id, $member_type, $start_time, $end_time);
    }

    /**
     * 获取本活动被分享次数
     * @param $prom_id
     * @param $activity_id
     * @param null $start_time
     * @param null $end_time
     * @return int|string
     */
    public static function getShareTimesEx($prom_id, $activity_id, $start_time=null, $end_time=null)
    {
        $query = PromotionShareLog::find()->select('prize_type,add_time')->where(['prom_id' => $prom_id]);
        if($activity_id!==null){
            $query->andWhere(['activity_id'=>(int)$activity_id]);
        }
        if(!empty($start_time)){
            $query->andWhere(['>','add_time',$start_time]);
        }
        if(!empty($end_time)){
            $query->andWhere(['<','add_time',$end_time]);
        }

        return $query->count();
    }

    /**
     * 获取会员分享本活动的次数
     * @param $prom_id
     * @param $activity_id
     * @param $member_id
     * @param string $member_type
     * @param null $start_time
     * @param null $end_time
     * @return int|string
     */
    public static function getMemShareTimesEx($prom_id, $activity_id, $member_id, $member_type='member_id', $start_time=null, $end_time=null)
    {
        $query = PromotionShareLog::find()->select('prize_type,add_time')->where(['prom_id' => $prom_id]);
        if($activity_id!==null){
            $query->andWhere(['activity_id'=>(int)$activity_id]);
        }
        if(!empty($start_time)){
            $query->andWhere(['>','add_time',$start_time]);
        }
        if(!empty($end_time)){
            $query->andWhere(['<','add_time',$end_time]);
        }

        switch($member_type){
            case 'member_id':
            case 'open_id':
            case 'phone':
                $query->andWhere([$member_type => $member_id]);
                break;
            default:
                $query->andWhere(['exch_type'=>(int)$member_type,'exch_value' => $member_id]);
        }

        return $query->count();
    }
}