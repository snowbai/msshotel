<?php

namespace common\models\order;

/**
 * 订单模块常量
 * Class OrderConst
 * @package common\models\order
 */
class OrderConst
{
    /**
     * 订单类型
     */
    const ORDER_ROOM = 1;
    const ORDER_PROMOTION = 2;
    const ORDER_DINNER = 3;
    const ORDER_EXT_MODULE = 4;

    /**
     * 订单状态标识位
     */
    const STATUS_BIT_CONFIRMED = 1 << 0;                        //确认状态

    const STATUS_BIT_PAY_ON_DELIVERY = 1 << 1;                  //是否到店支付
    const STATUS_BIT_PAY_PAID = 1 << 2;                         //支付状态
    const STATUS_BIT_PAY_REFUND_REQUESTED = 1 << 3;             //退款申请状态
    const STATUS_BIT_PAY_REFUND_CONFIRMED = 1 << 4;             //退款确认状态
    const STATUS_BIT_PAY_REFUND_DECLINED = 1 << 5;              //退款未通过状态
    const STATUS_BIT_PAY_REFUND_SUCCESS = 1 << 6;               //退款成功状态

    const STATUS_BIT_DELIVERY_SENT = 1 << 7;                    //发货状态（房间分配状态）
    const STATUS_BIT_DELIVERY_RECEIVED = 1 << 8;                //收货状态（房间入住状态）
    const STATUS_BIT_DELIVERY_RETURN_REQUESTED = 1 << 9;        //退货申请状态（退房申请状态）
    const STATUS_BIT_DELIVERY_RETURN_CONFIRMED = 1 << 10;        //退货确认状态（退房确认状态）
    const STATUS_BIT_DELIVERY_RETURN_DECLINED = 1 << 11;         //退货未通过状态（退房确认状态）
    const STATUS_BIT_DELIVERY_RETURN_SUCCESS = 1 << 12;         //退货成功状态（退房成功状态）

    const STATUS_BIT_DEALT_SUCCESS = 1 << 13;                   //成交状态
    const STATUS_BIT_DEALT_CANCELED = 1 << 14;                  //取消状态

    const STATUS_BIT_SMS_NOTIFIED = 1 << 15;                    //预定成功短信通知状态

}

