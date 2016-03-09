<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%hotelauth}}".
 *
 * @property string $id
 * @property string $g_id
 * @property string $h_id
 * @property string $hotel_role_id
 * @property string $sys_start_time
 * @property string $sys_end_time
 * @property string $sys_valid_start
 * @property integer $is_valid
 */
class Hotelauth extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotelauth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['g_id', 'h_id', 'hotel_role_id'], 'required'],
            [['g_id', 'h_id', 'hotel_role_id', 'is_valid'], 'integer'],
            [['sys_start_time', 'sys_end_time', 'sys_valid_start'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'g_id' => 'G ID',
            'h_id' => 'H ID',
            'hotel_role_id' => '酒店套餐角色',
            'sys_start_time' => '合同开始时间',
            'sys_end_time' => '合同结束时间',
            'sys_valid_start' => '系统上线时间，指完成对接，投入使用开始时间',
            'is_valid' => '1表示有效，2表示过期失效，3表示人工关闭系统',
        ];
    }
}
