<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%hotelsms_log}}".
 *
 * @property string $id
 * @property string $g_id
 * @property string $h_id
 * @property string $sms_add_num
 * @property string $md_admin_id
 * @property string $created_at
 */
class HotelsmsLog extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotelsms_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['g_id', 'h_id', 'sms_add_num', 'created_at'], 'required'],
            [['g_id', 'h_id', 'sms_add_num', 'md_admin_id', 'created_at'], 'integer']
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
            'sms_add_num' => '短信添加数',
            'md_admin_id' => '操作人',
            'created_at' => 'Created At',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'timestamp' => [
                    'class' => 'yii\behaviors\TimestampBehavior',
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ],
                ],
            ],
        ];
    }
}
