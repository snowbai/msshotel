<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_activity_turntable}}".
 *
 * @property integer $activity_id
 * @property integer $activity_type
 * @property integer $prom_id
 * @property string $first_prize_title
 * @property string $first_prize_name
 * @property integer $first_prize_num
 * @property double $first_prize_point
 * @property string $second_prize_title
 * @property string $second_prize_name
 * @property integer $second_prize_num
 * @property double $second_prize_point
 * @property string $third_prize_title
 * @property string $third_prize_name
 * @property integer $third_prize_num
 * @property double $third_prize_point
 * @property integer $info_show_level
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionActivityTurntable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_activity_turntable}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_type', 'prom_id', 'first_prize_num', 'second_prize_num', 'third_prize_num', 'info_show_level', 'status'], 'integer'],
            [['first_prize_point', 'second_prize_point', 'third_prize_point'], 'number'],
            [['modify_time', 'add_time'], 'safe'],
            [['first_prize_title', 'second_prize_title', 'third_prize_title'], 'string', 'max' => 32],
            [['first_prize_name', 'second_prize_name', 'third_prize_name'], 'string', 'max' => 64]
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
            'first_prize_title' => '一等奖奖项名称',
            'first_prize_name' => '一等奖奖品名称',
            'first_prize_num' => '一等奖奖品数量',
            'first_prize_point' => '一等奖中奖率',
            'second_prize_title' => '二等奖奖项名称',
            'second_prize_name' => '二等奖奖品名称',
            'second_prize_num' => '二等奖奖品数量',
            'second_prize_point' => '二等奖中奖率',
            'third_prize_title' => '三等奖奖项名称',
            'third_prize_name' => '三等奖奖品名称',
            'third_prize_num' => '三等奖奖品数量',
            'third_prize_point' => '三等奖中奖率',
            'info_show_level' => '信息显示级别',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
