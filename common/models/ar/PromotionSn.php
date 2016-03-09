<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_sn}}".
 *
 * @property integer $sn_id
 * @property string $sn_no
 * @property string $sn_pwd
 * @property integer $prom_id
 * @property integer $activity_id
 * @property integer $prize_id
 * @property integer $prize_type
 * @property string $prize_title
 * @property string $prize_name
 * @property integer $member_id
 * @property string $open_id
 * @property string $name
 * @property string $phone
 * @property integer $exch_type
 * @property string $exch_value
 * @property integer $status
 * @property integer $notify_status
 * @property string $allocate_time
 * @property string $expire_time
 * @property string $exchange_time
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionSn extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_sn}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prom_id', 'activity_id', 'prize_id', 'prize_type', 'member_id', 'exch_type', 'status', 'notify_status'], 'integer'],
            [['allocate_time', 'expire_time', 'exchange_time', 'modify_time', 'add_time'], 'safe'],
            [['sn_no'], 'string', 'max' => 30],
            [['sn_pwd', 'prize_title'], 'string', 'max' => 32],
            [['prize_name', 'open_id'], 'string', 'max' => 64],
            [['name', 'phone'], 'string', 'max' => 20],
            [['exch_value'], 'string', 'max' => 256],
            [['sn_no'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sn_id' => 'SN id',
            'sn_no' => 'sn码',
            'sn_pwd' => 'sn密码',
            'prom_id' => '活动ID',
            'activity_id' => '子活动id',
            'prize_id' => '奖品id',
            'prize_type' => '奖品类型',
            'prize_title' => '奖品标题',
            'prize_name' => '奖品名称',
            'member_id' => '兑奖会员ID',
            'open_id' => '兑奖微信Open Id',
            'name' => '兑奖姓名',
            'phone' => '兑奖手机号码',
            'exch_type' => '兑奖类型',
            'exch_value' => '兑奖信息',
            'status' => 'SN码状态',
            'notify_status' => '通知状态',
            'allocate_time' => '分配时间',
            'expire_time' => '过期时间',
            'exchange_time' => '兑换时间',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
