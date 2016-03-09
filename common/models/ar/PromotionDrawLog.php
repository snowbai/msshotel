<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_draw_log}}".
 *
 * @property integer $log_id
 * @property integer $prom_id
 * @property integer $activity_id
 * @property integer $prize_id
 * @property integer $prize_type
 * @property string $prize_sn
 * @property integer $member_id
 * @property string $open_id
 * @property string $phone
 * @property integer $exch_type
 * @property string $exch_value
 * @property string $extension_code
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionDrawLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_draw_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prom_id', 'activity_id', 'prize_id', 'prize_type', 'member_id', 'exch_type', 'status'], 'integer'],
            [['modify_time', 'add_time'], 'safe'],
            [['prize_sn'], 'string', 'max' => 30],
            [['open_id'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 20],
            [['exch_value', 'extension_code'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => '日志ID',
            'prom_id' => '活动ID',
            'activity_id' => '子活动id',
            'prize_id' => '奖品id',
            'prize_type' => '奖品类型',
            'prize_sn' => '奖品sn码',
            'member_id' => '会员ID',
            'open_id' => '微信Open Id',
            'phone' => '手机号码',
            'exch_type' => '兑奖类型',
            'exch_value' => '兑奖信息',
            'extension_code' => '扩展码',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
