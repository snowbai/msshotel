<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%hotelmss_permission}}".
 *
 * @property string $id
 * @property string $hotel_role_id
 * @property string $module_id
 * @property string $permission
 * @property string $created_at
 * @property string $updated_at
 */
class HotelmssPermission extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotelmss_permission}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hotel_role_id', 'module_id', 'permission', 'created_at', 'updated_at'], 'required'],
            [['hotel_role_id', 'module_id', 'permission', 'created_at', 'updated_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hotel_role_id' => 'Hotel Role ID',
            'module_id' => 'Module ID',
            'permission' => 'Permission',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'timestamp' => [
                    'class' => 'yii\behaviors\TimestampBehavior',
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_INSERT => [
                            'created_at', 'updated_at'
                        ],
                    ],
                ],
            ],
        ];
    }
}
