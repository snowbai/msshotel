<?php

namespace common\models\promotion\common\sn;

use common\models\base\Errorable;
use common\models\ar\PromotionSn;
use common\models\promotion\PromConst;
use common\models\promotion\common\sn\generator\SnGenerator;

/**
 * SN对象类
 * Class SnObj
 * @package common\models\promotion\sn
 * @author Lyl <inspii@me.com>
 * @version 2.0
 */
class SnObj extends Errorable
{
    /**
     * 错误码
     */
    const CODE_NO_VACANT = 10000; //没空空余的SN码
    const CODE_INVALID_TOKEN = 10001; //令牌错误

    const CODE_NOT_PENDING = 11000; //状态不是分配中
    const CODE_NOT_ALLOCATED = 11001; //状态不是已分配
    const CODE_NOT_GOT = 11002; //状态不是已领取
    const CODE_EMPTY_MEM_INFO = 12000; //会员信息为空
    const CODE_INVALID_PWD = 12000; //Sn密码错误
    const CODE_INVALID_MEM = 12000; //会员不一致

    /**
     * SN AR对象
     * @var PromotionSn
     */
    protected $_sn_ar;

    /**
     * 构造函数
     * SnObj constructor.
     * @param  $sn_ar
     */
    protected function __construct($sn_ar)
    {
        $this->_sn_ar = $sn_ar;
    }

    /**
     * 获取对象
     * @param $sn_no
     * @param int $sn_id
     * @param null $sn_ar
     * @return SnObj|null
     */
    public static function getInstance($sn_no, $sn_id=0, $sn_ar=null)
    {
        if(empty($sn_ar)){
            if($sn_id>0){
                $sn_ar = PromotionSn::findOne($sn_id);
            }elseif(!empty($sn_no)){
                $sn_ar = PromotionSn::findOne(['sn_no'=>$sn_no]);
            }
        }
        if(empty($sn_ar)) return null;

        return new SnObj($sn_ar);
    }

    /**
     * 创建一个SN码
     * @param $prom_id
     * @param $activity_id
     * @param $data
     * @param $algorithm
     * @return SnObj
     */
    public static function createSn($prom_id, $activity_id, $data, $algorithm=null)
    {
        $gen_data['prom_id'] = $prom_id;
        $gen_data['activity_id'] = $activity_id;
        $gen_data['data'] = $data;
        $sn_generator = new SnGenerator($algorithm);
        $sn_pwd = $sn_generator->generatePwd();

        $sn_ar = new PromotionSn();
        $sn_ar->setAttributes($data);
        $sn_ar->prom_id = $prom_id;
        $sn_ar->activity_id = $activity_id;
        $sn_ar->sn_pwd = $sn_pwd;

        $try_cnt = 0;
        do{
            $sn_ar->sn_no = $sn_generator->generateSn($gen_data);
            $success = $sn_ar->save();
            $try_cnt++;
            if($try_cnt > 20 && !$success){
                self::_pushStaticError(self::ERROR_LEVEL_SERVICE, self::CODE_SERVICE_OPEN, '尝试生成唯一SN码达到限制', $sn_ar->getErrors());
                return null;
            }
        }while(!$success);

        return new SnObj($sn_ar);
    }

    /**
     * 获取一个空余的SN
     * @param $prom_id
     * @param $activity_id
     * @param $prize_id
     * @param $rand_token
     * @param $use_prize_type
     * @return SnObj|null
     */
    public static function getAnVacant($prom_id, $activity_id, $prize_id, $rand_token, $use_prize_type=false)
    {
        //取得一个空余的SN码，并更改状态（防止并发）
        $db_conn = PromotionSN::getDb();
        $condition = "`prom_id`=$prom_id AND `activity_id`=$activity_id AND `status`=0 ";
        if($use_prize_type && $prize_id>0){
            $condition .= "AND `prize_type`=$prize_id";
        }elseif($prize_id>0){
            $condition .= "AND `prize_id`=$prize_id";
        }
        $params = "`exch_value`='" . $rand_token . "', `status`=" . PromConst::SN_STATUS_PENDING;
        $cmd = "UPDATE {{promotion_sn}} SET " . $params . " WHERE " . $condition . " LIMIT 1";
        $affect_rows = $db_conn->createCommand($cmd)->execute();
        if($affect_rows==0) return null;

        //返回对象
        $query = PromotionSn::find()->where(['prom_id'=>$prom_id, 'activity_id' => $activity_id, 'status'=>PromConst::SN_STATUS_PENDING]);
        if($use_prize_type && $prize_id>0){
            $query->andWhere(['prize_type'=>$prize_id]);
        }elseif($prize_id>0){
            $query->andWhere(['prize_id'=>$prize_id]);
        }
        $query->andWhere(['exch_value'=>$rand_token]);

        $sn_ar = $query->one();
        if(empty($sn_ar)) {
            self::_pushStaticError(self::ERROR_LEVEL_SERVICE, self::CODE_DB_GET, '获取空余SN码失败', null);
            return null;
        }else{
            return self::getInstance('', 0 , $sn_ar);
        }
    }

    /**
     * 获取SN信息
     * @return array
     */
    public function getInfo()
    {
        return $this->_sn_ar->toArray();
    }

    /**
     * 将SN设为空闲
     * @param $rand_token
     * @return bool
     */
    public function setVacant($rand_token)
    {
        if($this->_sn_ar->status != PromConst::SN_STATUS_PENDING){ //只有状态为分配中的SN才能设置状态为未分配
            $this->_pushError(self::ERROR_LEVEL_USER, self::CODE_NOT_PENDING, '非分配中状态的SN不能被设为空闲', null);
            return false;
        }elseif($this->_sn_ar->exch_value!=$rand_token){ //只有令牌正确才能被分配
            $this->_pushError(self::ERROR_LEVEL_USER, self::CODE_INVALID_TOKEN, '令牌不正确', null);
            return false;
        }

        $this->_sn_ar->exch_value = '';
        $this->_sn_ar->status = 0;
        if($this->_sn_ar->save()){
            return true;
        }else{
            $this->_pushError(self::ERROR_LEVEL_DATA, self::CODE_DB_UPDATE, '保存失败', $this->_sn_ar->getErrors());
            return false;
        }
    }

    /**
     * 分配SN
     * @param $mem_info
     * @param $rand_token
     * @return bool
     */
    public function give($mem_info,$rand_token)
    {
        if($this->_sn_ar->status != PromConst::SN_STATUS_PENDING){//只有状态为分配中才能被分配
            $this->_pushError(self::ERROR_LEVEL_USER, self::CODE_NOT_PENDING, '只有分配中的SN码才能被分配', null);
            return false;
        }elseif($this->_sn_ar->exch_value!=$rand_token){ //只有令牌正确才能被分配
            $this->_pushError(self::ERROR_LEVEL_USER, self::CODE_INVALID_TOKEN, '令牌不正确', null);
            return false;
        }

        $valid_mem_info = $this->_parseMemInfo($mem_info);
        if(empty($valid_mem_info)){
            $this->_pushError(self::ERROR_LEVEL_USER, self::CODE_EMPTY_MEM_INFO, '用户信息为空，不能分配', null);
            return false;
        }

        $this->_sn_ar->setAttributes($valid_mem_info);
        $this->_sn_ar->status = PromConst::SN_STATUS_ALLOCATED;
        if($this->_sn_ar->save()){
            return true;
        }else{
            $this->_pushError(self::ERROR_LEVEL_DATA, self::CODE_DB_UPDATE, '保存失败', $this->_sn_ar->getErrors());
            return false;
        }
    }

    /**
     * 领取奖品
     * @param $mem_info
     * @param bool $force
     * @return bool
     */
    public function get($mem_info, $force=false)
    {
        if($this->_sn_ar->status != PromConst::SN_STATUS_ALLOCATED) {//只有状态为分配的才能被领取
            $this->_pushError(self::ERROR_LEVEL_USER, self::CODE_NOT_ALLOCATED, '只有分配的SN码才能被领取', null);
            return false;
        }elseif(!$force && !$this->_isSameMem($mem_info, $this->sn_ar->toArray())){
            $this->_pushError(self::ERROR_LEVEL_USER, self::CODE_INVALID_MEM, '用户不一致', null);
            return false;
        }

        $valid_mem_info = $this->_parseMemInfo($mem_info);
        $this->_sn_ar->setAttributes($valid_mem_info);
        $this->_sn_ar->status = PromConst::SN_STATUS_GOT;
        if($this->_sn_ar->save()){
            return true;
        }else{
            $this->_pushError(self::ERROR_LEVEL_DATA, self::CODE_DB_UPDATE, '保存失败', $this->_sn_ar->getErrors());
            return false;
        }
    }

    /**
     * @param $sn_pwd
     * @param bool $force
     * @return bool
     */
    public function exchange($sn_pwd, $force=false)
    {
        if($this->_sn_ar->status != PromConst::SN_STATUS_GOT){//只有状态为已领取的才能被兑换
            $this->_pushError(self::ERROR_LEVEL_USER, self::CODE_NOT_GOT, '只有领取的SN码才能被兑换', null);
            return false;
        }elseif(!$force && $this->_sn_ar->sn_pwd != md5($sn_pwd)){ //只有密码正确才能被兑换
            $this->_pushError(self::ERROR_LEVEL_USER, self::CODE_INVALID_PWD, '只有密码正确才能兑换', null);
            return false;
        }

        $this->_sn_ar->status = PromConst::SN_STATUS_EXCHANGED;
        $this->_sn_ar->exchange_time = date('Y-m-d H:i:s');

        if($this->_sn_ar->save()){
            return true;
        }else{
            $this->_pushError(self::ERROR_LEVEL_DATA, self::CODE_DB_UPDATE, '保存失败', $this->_sn_ar->getErrors());
            return false;
        }
    }

    /**
     * 设置中奖SN信息已发送
     * @return bool
     */
    public function setNotified()
    {
        $this->_sn_ar->notify_status = 1;
        if($this->_sn_ar->save()){
            return true;
        }else{
            $this->_pushError(self::ERROR_LEVEL_DATA, self::CODE_DB_UPDATE, '保存失败', $this->_sn_ar->getErrors());
            return false;
        }
    }

    /**
     * 设置SN兑换已通知
     * @return bool
     */
    public function setExchangeNotified()
    {
        $this->_sn_ar->notify_status = 2;
        if($this->_sn_ar->save()){
            return true;
        }else{
            $this->_pushError(self::ERROR_LEVEL_DATA, self::CODE_DB_UPDATE, '保存失败', $this->_sn_ar->getErrors());
            return false;
        }
    }

    /**
     * 获取合法的用户信息
     * @param $mem_info
     * @return null
     */
    protected static function _parseMemInfo($mem_info)
    {
        $valid_info = null;
        if(isset($mem_info['member_id'])){
            $valid_info['member_id'] = $mem_info['member_id'];
        }
        if(isset($mem_info['open_id'])){
            $valid_info['open_id'] = $mem_info['open_id'];
        }
        if(isset($mem_info['phone'])){
            $valid_info['phone'] = $mem_info['phone'];
        }
        if(isset($mem_info['name'])){
            $valid_info['name'] = $mem_info['name'];
        }
        if(isset($mem_info['exch_type']) && isset($mem_info['exch_value'])){
            $valid_info['exch_type'] = $mem_info['exch_type'];
            $valid_info['exch_value'] = $mem_info['exch_value'];
        }

        return $valid_info;
    }

    /**
     * 判断会员是否一致
     * @param $mem_info1
     * @param $mem_info2
     * @return bool
     */
    protected static function _isSameMem($mem_info1, $mem_info2)
    {
        if(isset($mem_info1['member_id']) && isset($mem_info2['member_id']))
        {
            if($mem_info1['member_id'] == $mem_info2['member_id']) return true;
        }
        if(isset($mem_info1['open_id']) && isset($mem_info2['open_id']))
        {
            if($mem_info1['open_id'] == $mem_info2['open_id']) return true;
        }
        if(isset($mem_info1['phone']) && isset($mem_info2['phone']))
        {
            if($mem_info1['phone'] == $mem_info2['phone']) return true;
        }
        if(isset($mem_info1['name']) && isset($mem_info2['name']))
        {
            if($mem_info1['name'] == $mem_info2['name']) return true;
        }
        if(isset($mem_info1['exch_type']) && isset($mem_info2['exch_type'])
            && isset($mem_info1['exch_type']) && isset($mem_info2['exch_type']))
        {
            if($mem_info1['exch_type'] == $mem_info2['exch_type'] && $mem_info1['exch_type'] > 0
                && $mem_info1['exch_value'] == $mem_info2['exch_value'] ) return true;
        }

        return false;
    }
}