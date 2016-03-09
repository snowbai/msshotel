<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_activity_vote_options}}".
 *
 * @property integer $option_id
 * @property integer $option_type
 * @property integer $activity_id
 * @property string $title
 * @property string $name
 * @property string $description
 * @property integer $votes
 * @property string $other_info
 * @property integer $list_order
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionActivityVoteOptions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_activity_vote_options}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['option_type', 'activity_id', 'votes', 'list_order', 'status'], 'integer'],
            [['modify_time', 'add_time'], 'safe'],
            [['title'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 128],
            [['other_info'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'option_id' => '选项ID',
            'option_type' => '选项类型',
            'activity_id' => '投票活动activity_id',
            'title' => '选项标题',
            'name' => '选项名称',
            'description' => '选项描述',
            'votes' => '投票数量',
            'other_info' => '其他信息',
            'list_order' => '排序',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
