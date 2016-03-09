<?php

namespace common\models\promotion\proms_join;

use common\models\promotion\PromConst;
use common\models\ar\Promotion as PromotionAR;
use common\models\ar\PromotionActivityVote as VoteAR;
use common\models\ar\PromotionActivityVoteLog as LogAR;
use common\models\ar\PromotionActivityVoteOptions as OptionsAR;
use common\models\ar\PromotionActivityVotePrize as PrizeAR;
use common\models\promotion\proms_join\common\DoCheck;
use common\models\promotion\common\statistics\PvStats;

/**
 * 促销活动参与类
 * Class VoteJoin
 * @package common\models\promotion\proms_join
 */
class VoteJoin implements IPromotionJoin
{
    /*
     * 投票类型
     */
    const VOTE_TYPE_TEXT = 0;
    const VOTE_TYPE_IMAGE = 1;

    /*
     * 错误码
     */
    const CODE_SUCCESS = 0;
    const CODE_NOT_SUPPORTED = 1;
    const CODE_NOT_STARTED = 2;
    const CODE_OVER = 3;
    const CODE_PROM_INVALID = 4;
    const CODE_SELECTION_EXCEED = 5;
    const CODE_REACH_DAY_LIMIT = 6;
    const CODE_REACH_TOTAL_LIMIT = 7;
    const CODE_SAVE_FAILED = 8;

    use DoCheck;

    /**
     * 活动ID
     * @var PromotionAR
     */
    protected $_prom_ar;

    /**
     * 具体活动AR
     * @var VoteAR
     */
    protected $_act_ar;

    /**
     * 构造函数
     * VoteJoin constructor.
     * @param $prom_ar
     * @param $act_ar
     */
    protected function __construct($prom_ar, $act_ar)
    {
        $this->_prom_ar = $prom_ar;
        $this->_act_ar = $act_ar;
    }

    public static function getInstance($prom_id)
    {
        $prom_ar = PromotionAR::findOne((int)$prom_id);
        if(empty($prom_ar)) return null;
        $act_ar = VoteAR::findOne($prom_id);
        if(empty($act_ar)) return null;

        return new VoteJoin($prom_ar, $act_ar);
    }

    public function getInfo()
    {
        $info['promotion'] = $this->_prom_ar->toArray();
        $info['activity'] = $this->_act_ar->toArray();

        return $info;
    }

    /**
     * 获取统计数据
     * @return mixed
     */
    public function getStatistics()
    {
        $option_num = OptionsAR::find()->where(['activity_id'=>$this->_act_ar->activity_id])->andWhere(['<>', 'status', PromConst::STATUS_DELETED])->count();
        $vote_num = LogAR::find()->where(['activity_id'=>$this->_act_ar->activity_id])->count(); //获取用户参与次数（按日志记录数算）

        $statistic['option_num'] = $option_num;
        $statistic['vote_num'] = $vote_num;

        return $statistic;
    }

    /**
     * 获取选项列表
     * @param $search_data
     * @param int $offset
     * @param null $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getOptions($search_data, $offset=0, $limit=null)
    {
        $option_query = OptionsAR::find()->where(['activity_id'=>$this->_act_ar->activity_id])
            ->andWhere(['<>', 'status', PromConst::STATUS_DELETED]);

        if(is_string($search_data)){
            $option_query->andWhere(['like', 'title', $search_data]);
        }

        $option_list = $option_query->orderBy('list_order ASC')->offset($offset)->limit($limit)->asArray()->all();

        //获取图片
        if($this->_act_ar->activity_type == self::VOTE_TYPE_IMAGE){
            foreach($option_list as $k=>$option){
                $option_list[$k]['img'] = 'http://f3.v.veimg.cn/medeen/uploads/1/20151208170904861.jpg';
            }
        }

        return $option_list;
    }

    /**
     * 获取奖项列表
     * @param $offset
     * @param null $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getPrizeList($offset=0, $limit=null)
    {
        $prize_list = PrizeAR::find()->select('prize_type, title, name')
            ->where(['activity_id'=>$this->_act_ar->activity_id])
            ->andWhere(['<>', 'status', PromConst::STATUS_DELETED])
            ->orderBy('prize_type, add_time ASC')
            ->offset($offset)->limit($limit)
            ->asArray()->all();

        return $prize_list;
    }


    public function vote($option_id, $member_id,$member_type='member_id')
    {
        if(!self::_checkPromValid($this->_prom_ar)) return ['code'=>self::CODE_PROM_INVALID,'msg'=>'活动未开始或已结束'];


    }

    /**
     * 获取浏览量
     * @return int|mixed
     */
    public function getPv()
    {
        $pv_obj = new PvStats($this->_prom_ar->prom_id, $this->_act_ar->activity_id);
        return $pv_obj->getPv();
    }

    /**
     * 更新浏览量
     * @return bool
     */
    public function updatePv()
    {
        $pv_obj = new PvStats($this->_prom_ar->prom_id, $this->_act_ar->activity_id);
        return $pv_obj->updatePv();
    }

    /**
     * 获取投票排名（使用静态方法，避免构造函数执行两次非必要查询）
     * @param $act_id
     * @param int $offset
     * @param null $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRankList($act_id, $offset=0, $limit=null)
    {
        $rank_list = OptionsAR::find()->select('title, name, votes')
            ->where(['activity_id'=>$act_id])
            ->andWhere(['<>', 'status', PromConst::STATUS_DELETED])
            ->orderBy('votes DESC')
            ->offset($offset)->limit($limit)
            ->asArray()->all();

        return $rank_list;
    }

    /**
     * 对会员投票进行记录
     * @param string $act_id
     * @param $type
     * @param $option_ids
     * @param $member_id
     * @param $member_type
     * @return bool
     */
    protected static function _log($act_id, $type, $option_ids, $member_id, $member_type)
    {
        $log_obj = new LogAR();
        $log_obj->activity_id = $act_id;
        if($type=='text'){
            $log_obj->option_ids = $option_ids;
        }else{
            $log_obj->option_id = intval($option_ids);
        }
        if($member_type=='member_id' || $member_type=='open_id' || $member_type=='phone') {
            $log_obj->$member_type = $member_id;
        }else{
            $log_obj->exch_type = intval($member_type);
            $log_obj->exch_value = $member_id;
        }
        return $log_obj->save();
    }

    /**
     * 获取投票次数
     * @param string $act_id
     * @param string $option_id
     * @param string $member_id
     * @param string $member_type
     * @param string $start_time
     * @param string $end_time
     * @return int|string
     */
    protected static function _getVoteCounts($act_id, $option_id='', $member_id='', $member_type='', $start_time='', $end_time='')
    {
        $count_query = LogAR::find()->where(['activity_id'=>$act_id]);
        if(!empty($option_id)){
            $count_query->andWhere(['option_id'=>$option_id]);
        }
        if(!empty($member_id) && !empty($member_type)){
            if($member_type=='member_id' || $member_type=='open_id' || $member_type=='phone') {
                $count_query->andWhere([$member_type => $member_id]);
            }else{
                $count_query->andWhere(['exch_type'=>intval($member_type),'exch_value'=>intval($member_id)]);
            }
        }
        if(!empty($start_time)){
            $count_query->andWhere(['>', 'add_time', $start_time]);
        }
        if(!empty($end_time)){
            $count_query->andWhere(['<', 'add_time', $end_time]);
        }

        $counts = $count_query->count();

        return $counts;
    }
}