<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_share_log}}".
 *
 * @property integer $log_id
 * @property integer $prom_id
 * @property integer $activity_id
 * @property integer $member_id
 * @property string $open_id
 * @property string $phone
 * @property integer $exch_type
 * @property string $exch_value
 * @property string $share_info
 * @property integer $share_cnt
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionShareLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_share_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prom_id', 'activity_id', 'member_id', 'exch_type', 'share_cnt', 'status'], 'integer'],
            [['modify_time', 'add_time'], 'safe'],
            [['open_id'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 20],
            [['exch_value', 'share_info'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => '分享日志ID',
            'prom_id' => '活动ID',
            'activity_id' => '子活动id',
            'member_id' => '会员ID',
            'open_id' => '微信Open Id',
            'phone' => '手机号码',
            'exch_type' => '兑奖类型',
            'exch_value' => '兑奖信息',
            'share_info' => '分享信息',
            'share_cnt' => '分享次数',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
