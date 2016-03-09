<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_prize}}".
 *
 * @property integer $prize_id
 * @property integer $prize_type
 * @property integer $prom_id
 * @property integer $activity_id
 * @property string $title
 * @property string $name
 * @property string $description
 * @property integer $num
 * @property integer $remain_num
 * @property double $point
 * @property string $other_info
 * @property integer $status
 * @property string $expire_time
 * @property string $modify_time
 * @property string $add_time
 */
class PromotionPrize extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_prize}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prize_type', 'prom_id', 'activity_id', 'num', 'remain_num', 'status'], 'integer'],
            [['point'], 'number'],
            [['expire_time', 'modify_time', 'add_time'], 'safe'],
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
            'prize_id' => '奖品id',
            'prize_type' => '奖品类型',
            'prom_id' => '活动ID',
            'activity_id' => '子活动id',
            'title' => '奖品标题',
            'name' => '奖品名称',
            'description' => '奖品描述',
            'num' => '奖品数量',
            'remain_num' => '奖品剩余数量（若使用SN，则本字段无效）',
            'point' => '中奖率',
            'other_info' => '其他信息',
            'status' => '状态',
            'expire_time' => '过期时间',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
