<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_activity_scratch}}".
 *
 * @property integer $activity_id
 * @property integer $activity_type
 * @property integer $prom_id
 * @property integer $info_show_level
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionActivityScratch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_activity_scratch}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_type', 'prom_id', 'info_show_level', 'status'], 'integer'],
            [['modify_time', 'add_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'activity_id' => '子活动ID',
            'activity_type' => '子活动类型',
            'prom_id' => '活动ID',
            'info_show_level' => '信息显示级别',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
