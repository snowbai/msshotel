<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_limit}}".
 *
 * @property integer $lmt_id
 * @property integer $prom_id
 * @property integer $activity_id
 * @property integer $lmt_level
 * @property integer $total_lmt
 * @property integer $day_lmt
 * @property integer $hour_lmt
 * @property integer $total_win_lmt
 * @property integer $day_win_lmt
 * @property integer $hour_win_lmt
 * @property integer $mem_total_lmt
 * @property integer $mem_day_lmt
 * @property integer $mem_hour_lmt
 * @property integer $mem_total_win_lmt
 * @property integer $mem_day_win_lmt
 * @property integer $mem_hour_win_lmt
 * @property integer $status
 * @property integer $extension_type
 * @property string $extension_value
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionLimit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_limit}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prom_id', 'activity_id', 'lmt_level', 'total_lmt', 'day_lmt', 'hour_lmt', 'total_win_lmt', 'day_win_lmt', 'hour_win_lmt', 'mem_total_lmt', 'mem_day_lmt', 'mem_hour_lmt', 'mem_total_win_lmt', 'mem_day_win_lmt', 'mem_hour_win_lmt', 'status', 'extension_type'], 'integer'],
            [['modify_time', 'add_time'], 'safe'],
            [['extension_value'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lmt_id' => '限制ID',
            'prom_id' => '活动ID',
            'activity_id' => '子活动id',
            'lmt_level' => '限制级别',
            'total_lmt' => '活动期间抽奖限制次数',
            'day_lmt' => '每天抽奖限制次数',
            'hour_lmt' => '每小时抽奖限制次数',
            'total_win_lmt' => '活动期间中奖限制次数',
            'day_win_lmt' => '每天中奖限制次数',
            'hour_win_lmt' => '每小时限制次数',
            'mem_total_lmt' => '会员活动期间抽奖限制次数',
            'mem_day_lmt' => '会员每天抽奖限制次数',
            'mem_hour_lmt' => '会员每小时抽奖限制次数',
            'mem_total_win_lmt' => '会员活动期间中奖限制次数',
            'mem_day_win_lmt' => '会员每天中奖限制次数',
            'mem_hour_win_lmt' => '会员每小时中奖限制次数',
            'status' => '状态',
            'extension_type' => '扩展类型',
            'extension_value' => '扩展值',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
