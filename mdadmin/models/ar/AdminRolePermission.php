<?php

namespace mdadmin\models\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%admin_rolepermission}}".
 *
 * @property string $id
 * @property string $mdadmin_role_id
 * @property string $module_id
 * @property string $permission
 * @property string $created_at
 * @property string $updated_at
 */
class AdminRolePermission extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_rolepermission}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mdadmin_role_id', 'module_id', 'permission', 'created_at', 'updated_at'], 'required'],
            [['mdadmin_role_id', 'module_id', 'permission', 'created_at', 'updated_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mdadmin_role_id' => 'Mdadmin Role ID',
            'module_id' => 'Module ID',
            'permission' => 'Permission',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [

                'timestamp' => [
                    'class' => 'yii\behaviors\TimestampBehavior',
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_INSERT => [
                            'created_at', 'updated_at'
                        ],
                    ],
                ],
            
        ];
    }
}
