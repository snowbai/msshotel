<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $order_id
 * @property string $order_no
 * @property integer $order_type
 * @property string $order_pid
 * @property integer $product_type
 * @property integer $product_subtype
 * @property integer $g_id
 * @property integer $h_id
 * @property integer $member_id
 * @property integer $member_grade
 * @property string $apply_name
 * @property string $apply_phone
 * @property string $apply_price
 * @property integer $pay_type
 * @property integer $refer_type
 * @property integer $order_status
 * @property string $add_time
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_type', 'order_pid', 'product_type', 'product_subtype', 'g_id', 'h_id', 'member_id', 'member_grade', 'pay_type', 'refer_type', 'order_status'], 'integer'],
            [['apply_price'], 'number'],
            [['add_time'], 'safe'],
            [['order_no'], 'string', 'max' => 30],
            [['apply_name'], 'string', 'max' => 32],
            [['apply_phone'], 'string', 'max' => 20],
            [['order_no'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单ID',
            'order_no' => '订单编号',
            'order_type' => '订单类型，1 已续住订单 2 续住订单',
            'order_pid' => '父ID',
            'product_type' => '购买产品类型',
            'product_subtype' => '购买产品子类型',
            'g_id' => '集团id',
            'h_id' => '门店id',
            'member_id' => '会员id',
            'member_grade' => '下单时会员等级',
            'apply_name' => '联系人',
            'apply_phone' => '移动电话',
            'apply_price' => '总价',
            'pay_type' => '支付类型',
            'refer_type' => '订单来源类型',
            'order_status' => '订单确认状态',
            'add_time' => '添加时间',
        ];
    }
}
