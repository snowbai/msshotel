<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_pv}}".
 *
 * @property integer $pv_id
 * @property integer $prom_id
 * @property integer $activity_id
 * @property integer $total_views
 * @property integer $from_1
 * @property integer $from_2
 * @property integer $from_3
 * @property string $details
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionPv extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_pv}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prom_id', 'activity_id', 'total_views', 'from_1', 'from_2', 'from_3', 'status'], 'integer'],
            [['details'], 'string'],
            [['modify_time', 'add_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pv_id' => '统计ID',
            'prom_id' => '活动ID',
            'activity_id' => '子活动ID',
            'total_views' => '页面访问量',
            'from_1' => '来源1访问量',
            'from_2' => '来源2访问量',
            'from_3' => '来源3访问量',
            'details' => '详细信息',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
