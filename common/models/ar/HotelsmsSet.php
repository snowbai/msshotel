<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%hotelsms_set}}".
 *
 * @property string $id
 * @property string $g_id
 * @property string $h_id
 * @property string $sms_total_num
 * @property string $sms_send_num
 */
class HotelsmsSet extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotelsms_set}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['g_id', 'h_id', 'sms_total_num', 'sms_send_num'], 'required'],
            [['g_id', 'h_id', 'sms_total_num', 'sms_send_num'], 'integer']
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
            'sms_total_num' => 'Sms Total Num',
            'sms_send_num' => 'Sms Send Num',
        ];
    }
}
