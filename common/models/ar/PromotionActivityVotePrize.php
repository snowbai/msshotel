<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_activity_vote_prize}}".
 *
 * @property integer $prize_id
 * @property integer $activity_id
 * @property integer $prize_type
 * @property string $title
 * @property string $name
 * @property string $description
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionActivityVotePrize extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_activity_vote_prize}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'prize_type', 'status'], 'integer'],
            [['modify_time', 'add_time'], 'safe'],
            [['title'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'prize_id' => '奖品id',
            'activity_id' => '投票活动activity_id',
            'prize_type' => '奖品类型（负数为出错）',
            'title' => '奖品标题',
            'name' => '奖品名称',
            'description' => '奖品描述',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
