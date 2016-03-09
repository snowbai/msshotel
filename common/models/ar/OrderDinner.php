<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%order_dinner}}".
 *
 * @property string $id
 * @property string $order_id
 * @property string $order_no
 * @property integer $order_type
 * @property string $order_pid
 * @property integer $g_id
 * @property integer $h_id
 * @property integer $dinner_type
 * @property integer $dinner_id
 * @property string $dinner_name
 * @property string $dinner_market_price
 * @property string $dinner_price
 * @property string $dinner_attr
 * @property integer $member_id
 * @property integer $member_grade
 * @property string $apply_name
 * @property string $apply_phone
 * @property string $apply_request
 * @property string $apply_price
 * @property integer $apply_num
 * @property string $apply_arrive_date
 * @property string $apply_arrive_hour
 * @property integer $member_integral_used
 * @property string $apply_integral_money
 * @property integer $member_discount_value
 * @property string $apply_discount_money
 * @property string $other_discount_info
 * @property string $allocate_info
 * @property integer $pay_type
 * @property string $pay_id
 * @property string $pay_money
 * @property string $note
 * @property string $to_buyer
 * @property integer $refer_type
 * @property string $refer_value
 * @property string $order_detail
 * @property integer $extension_type
 * @property string $extension_value
 * @property integer $order_status
 * @property string $modify_time
 * @property string $add_time
 */
class OrderDinner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_dinner}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'order_type', 'order_pid', 'g_id', 'h_id', 'dinner_type', 'dinner_id', 'member_id', 'member_grade', 'apply_num', 'member_integral_used', 'member_discount_value', 'pay_type', 'pay_id', 'refer_type', 'extension_type', 'order_status'], 'integer'],
            [['dinner_market_price', 'dinner_price', 'apply_price', 'apply_integral_money', 'apply_discount_money', 'pay_money'], 'number'],
            [['apply_arrive_date', 'apply_arrive_hour', 'modify_time', 'add_time'], 'safe'],
            [['order_no'], 'string', 'max' => 30],
            [['dinner_name'], 'string', 'max' => 64],
            [['dinner_attr', 'allocate_info', 'refer_value', 'order_detail', 'extension_value'], 'string', 'max' => 512],
            [['apply_name'], 'string', 'max' => 32],
            [['apply_phone'], 'string', 'max' => 20],
            [['apply_request', 'other_discount_info', 'note', 'to_buyer'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单ID（取自order表）',
            'order_no' => '订单编号（取自order表）',
            'order_type' => '订单类型',
            'order_pid' => '父ID',
            'g_id' => '集团id',
            'h_id' => '门店id',
            'dinner_type' => '餐饮类型',
            'dinner_id' => '餐饮id',
            'dinner_name' => '餐饮名称',
            'dinner_market_price' => '餐饮市场价',
            'dinner_price' => '餐饮单价',
            'dinner_attr' => '餐饮参数',
            'member_id' => '会员id',
            'member_grade' => '下单时会员等级',
            'apply_name' => '联系人',
            'apply_phone' => '移动电话',
            'apply_request' => '特殊要求',
            'apply_price' => '总价',
            'apply_num' => '购买数量',
            'apply_arrive_date' => '到店日期（冗余）',
            'apply_arrive_hour' => '到店时间',
            'member_integral_used' => '使用会员积分数量',
            'apply_integral_money' => '使用会员积分抵扣金额',
            'member_discount_value' => '会员折扣值',
            'apply_discount_money' => '使用会员等级抵扣金额',
            'other_discount_info' => '其他折扣信息',
            'allocate_info' => '分配信息',
            'pay_type' => '支付类型',
            'pay_id' => '支付id',
            'pay_money' => '支付价格',
            'note' => '后台备注',
            'to_buyer' => '给客户留言',
            'refer_type' => '订单来源类型',
            'refer_value' => '订单来源',
            'order_detail' => '订单详情',
            'extension_type' => '扩展类型',
            'extension_value' => '扩展值',
            'order_status' => '订单确认状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
