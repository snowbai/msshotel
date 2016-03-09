<?php

namespace mssadmin\models\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%mssadmin_role}}".
 *
 * @property string $id
 * @property string $g_id
 * @property string $h_id
 * @property string $role_name
 * @property string $role_description
 * @property integer $created_at
 * @property integer $updated_at
 */
class MssadminRole extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mssadmin_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['g_id', 'h_id', 'role_name', 'role_description', 'created_at', 'updated_at'], 'required'],
            [['g_id', 'h_id', 'created_at', 'updated_at'], 'integer'],
            [['role_description'], 'string'],
            [['role_name'], 'string', 'max' => 20]
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
            'role_name' => '角色名',
            'role_description' => '描述',
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
