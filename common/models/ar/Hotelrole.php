<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%hotelrole}}".
 *
 * @property string $hotel_role_id
 * @property string $hotel_role_name
 * @property string $hotel_role_description
 * @property string $created_at
 * @property string $updated_at
 */
class Hotelrole extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotelrole}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hotel_role_name', 'hotel_role_description', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['hotel_role_name'], 'string', 'max' => 50],
            [['hotel_role_description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hotel_role_id' => 'Hotel Role ID',
            'hotel_role_name' => '角色名',
            'hotel_role_description' => '随便填',
            'created_at' => '角色创建时间',
            'updated_at' => '角色修改时间',
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
