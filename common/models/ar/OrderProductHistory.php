<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%order_product_history}}".
 *
 * @property string $history_id
 * @property string $order_id
 * @property string $order_no
 * @property integer $product_type
 * @property integer $product_subtype
 * @property integer $product_id
 * @property string $snapshop
 * @property string $diff
 * @property string $note
 * @property integer $action_user_id
 * @property string $action_user_name
 * @property string $action_note
 * @property integer $customer_notified
 * @property string $add_time
 */
class OrderProductHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_product_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'product_type', 'product_subtype', 'product_id', 'action_user_id', 'customer_notified'], 'integer'],
            [['snapshop', 'diff'], 'string'],
            [['add_time'], 'safe'],
            [['order_no'], 'string', 'max' => 30],
            [['note', 'action_note'], 'string', 'max' => 128],
            [['action_user_name'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'history_id' => 'ID',
            'order_id' => '订单ID（取自order表）',
            'order_no' => '订单编号（取自order表）',
            'product_type' => '产品类型',
            'product_subtype' => '产品子类型',
            'product_id' => '产品ID',
            'snapshop' => '产品快照',
            'diff' => '差异信息',
            'note' => '差异说明',
            'action_user_id' => '操作人员id',
            'action_user_name' => '操作人员姓名',
            'action_note' => '操作信息',
            'customer_notified' => '客户通知状态',
            'add_time' => '添加时间',
        ];
    }
}
