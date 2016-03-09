<?php

namespace common\models\promotion;

/**
 * Class PromConst
 * @package common\models\promotion
 */
class PromConst
{
    /**
     * 活动常量
     */
    //活动类型
    CONST PROMOTION_TYPE_ORDINARY = 0;      //普通活动
    CONST PROMOTION_TYPE_TURNTABLE = 1;     //大转盘活动
    CONST PROMOTION_TYPE_SCRATCH = 2;       //刮刮乐活动
    CONST PROMOTION_TYPE_SECKILL = 3;       //秒杀活动（在线支付）
    CONST PROMOTION_TYPE_VOTE = 4;

    //活动限制级别
    CONST LIMIT_NONE = 0;                   //无限制
    CONST LIMIT_MEM_BY_HOUR = 1;            //对会员每小时抽奖及中奖次数进行限制
    CONST LIMIT_MEM_BY_DAY = 2;             //对会员每天抽奖及中奖次数进行限制
    CONST LIMIT_MEM_BY_DURATION = 3;        //对会员活动期间抽奖及中奖次数进行限制
    CONST LIMIT_MEM_BY_DAY_AND_DURATION = 4;//对会员活动期间及每天抽奖及中奖次数进行限制
    CONST LIMIT_MEM_BY_HOUR_DAY_AND_DURATION = 5;//对会员活动期间、每天和每小时抽奖及中奖次数进行限制
    CONST LIMIT_BY_HOUR = 11;               //限制每小时抽奖及中奖次数
    CONST LIMIT_BY_DAY = 12;                //限制每天抽奖及中奖次数
    CONST LIMIT_BY_DURATION = 13;           //限制活动期间抽奖及中奖次数
    CONST LIMIT_BY_DAY_AND_DURATION = 14;   //限制活动期间和每天抽奖及中奖次数
    CONST LIMIT_BY_HOUR_DAY_AND_DURATION = 15;//限制活动期间，每天及每小时抽奖及中奖次数
    CONST LIMIT_ALL_BY_HOUR = 21;               //限制活动及会员每小时抽奖及中奖次数
    CONST LIMIT_ALL_BY_DAY = 22;                //限制活动及会员每天抽奖及中奖次数
    CONST LIMIT_ALL_BY_DURATION = 23;           //限制活动及会员活动期间抽奖及中奖次数
    CONST LIMIT_ALL_BY_DAY_AND_DURATION = 24;   //限制活动及会员活动期间和每天抽奖及中奖次数
    CONST LIMIT_ALL_BY_HOUR_DAY_AND_DURATION = 25;//限制活动及会员活动期间，每天及每小时抽奖及中奖次数

    //活动参与权限
    CONST PERM_MEM = 0;
    CONST PERM_ANONYMOUS = 1;
    CONST PERM_WX_AUTH = 2;
    CONST PERM_FOLLOW = 3;
    CONST PERM_WX_AUTH_AND_FOLLOW =10;

    /**
     * SN，奖品等常量
     */
    //SN码状态值
    CONST SN_STATUS_VACANT = 0;             //未占用
    CONST SN_STATUS_PENDING = 50;           //分配中
    CONST SN_STATUS_ALLOCATED = 51;         //已分配
    CONST SN_STATUS_GOT = 52;               //已领取
    CONST SN_STATUS_EXCHANGED = 53;         //已兑换

    //奖品类型
    CONST PRIZE_NONE = 0;                   //未中奖
    CONST PRIZE_TRY_AGAIN = -1;             //再来一次
    CONST PRIZE_REACH_WIN_LIMIT = -2;       //因中奖次数达限制未能中奖
    CONST PRIZE_TODAY_OUT = -3;             //今天奖品已经发完
    CONST PRIZE_INVALID_SIGN = -4;          //因签名错误而未能中奖
    CONST PRIZE_GET_FAILED = -4;            //因分配奖品失败而未能中奖
    CONST PRIZE_NOT_LOGIN = -10;            //因未登录而不能抽奖（不写入日志)
    CONST PRIZE_REACH_DRAW_LIMIT = -11;     //因抽奖次数达到限制未能抽奖
    CONST PRIZE_DATA_INVALID = -12;         //因提交数据非法不能抽奖（不写入日志）
    CONST PRIZE_PROM_INVALID = -13;         //因活动非法不能抽奖（不写入日志）
    CONST PRIZE_SEVER_ERROR = -20;          //服务器错误（不写入日志）

    //奖品限制错误码
    CONST ERR_PRIZE_WIN_LIMIT_REACHED = -self::PRIZE_REACH_WIN_LIMIT;   //中奖次数达到限制错误
    CONST ERR_PRIZE_DRAW_LIMIT_REACHED = -self::PRIZE_REACH_DRAW_LIMIT; //抽奖次数达到限制错误

}
