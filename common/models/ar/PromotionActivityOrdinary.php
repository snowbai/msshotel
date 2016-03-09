<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_activity_ordinary}}".
 *
 * @property integer $activity_id
 * @property integer $activity_type
 * @property integer $prom_id
 * @property integer $num
 * @property integer $remain_num
 * @property string $market_price
 * @property string $price
 * @property integer $info_show_level
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionActivityOrdinary extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_activity_ordinary}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_type', 'prom_id', 'num', 'remain_num', 'info_show_level', 'status'], 'integer'],
            [['market_price', 'price'], 'number'],
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
            'num' => '奖品设置数量',
            'remain_num' => '奖品剩余数量',
            'market_price' => '市场价格',
            'price' => '当天价格',
            'info_show_level' => '信息显示级别',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
