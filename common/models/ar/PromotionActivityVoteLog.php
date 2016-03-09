<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_activity_vote_log}}".
 *
 * @property integer $log_id
 * @property integer $activity_id
 * @property integer $option_id
 * @property string $option_ids
 * @property integer $option_type
 * @property integer $member_id
 * @property string $open_id
 * @property string $phone
 * @property integer $exch_type
 * @property string $exch_value
 * @property string $add_time
 */
class PromotionActivityVoteLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_activity_vote_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'option_id', 'option_type', 'member_id', 'exch_type'], 'integer'],
            [['add_time'], 'safe'],
            [['option_ids', 'exch_value'], 'string', 'max' => 256],
            [['open_id'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => '日志ID',
            'activity_id' => '投票活动activity_id',
            'option_id' => '选项id（图片投票使用）',
            'option_ids' => '选项ids（文字投票使用）',
            'option_type' => '选项类型',
            'member_id' => '会员ID',
            'open_id' => '微信Open Id',
            'phone' => '手机号码',
            'exch_type' => '兑奖类型',
            'exch_value' => '兑奖信息',
            'add_time' => '添加时间',
        ];
    }
}
