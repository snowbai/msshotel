<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_activity_seckill}}".
 *
 * @property integer $activity_id
 * @property integer $activity_type
 * @property integer $prom_id
 * @property string $prize_title
 * @property string $prize_name
 * @property string $market_price
 * @property string $price
 * @property integer $num
 * @property integer $remain_num
 * @property string $start_time
 * @property string $end_time
 * @property integer $mem_day_lmt_num
 * @property integer $mem_day_win_lmt_num
 * @property integer $use_advanced_lmt
 * @property integer $info_show_level
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionActivitySeckill extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_activity_seckill}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_type', 'prom_id', 'num', 'remain_num', 'mem_day_lmt_num', 'mem_day_win_lmt_num', 'use_advanced_lmt', 'info_show_level', 'status'], 'integer'],
            [['market_price', 'price'], 'number'],
            [['start_time', 'end_time', 'modify_time', 'add_time'], 'safe'],
            [['prize_title'], 'string', 'max' => 32],
            [['prize_name'], 'string', 'max' => 64]
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
            'prize_title' => '奖品标题',
            'prize_name' => '奖品名称',
            'market_price' => '市场价格',
            'price' => '秒杀价格',
            'num' => '奖品设置数量',
            'remain_num' => '奖品剩余数量',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'mem_day_lmt_num' => '会员每天限制数量',
            'mem_day_win_lmt_num' => '会员每天中奖限制次数',
            'use_advanced_lmt' => '是否使用高级限制功能',
            'info_show_level' => '信息显示级别',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
