<?php

namespace common\models\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%hoteladmin_permission}}".
 *
 * @property string $id
 * @property string $hotel_role_id
 * @property string $module_id
 * @property string $controller_id
 * @property string $action_id
 * @property string $permission
 * @property integer $type
 * @property string $link
 * @property string $title
 * @property string $parent_id
 */
class HoteladminPermission extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hoteladmin_permission}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hotel_role_id', 'module_id', 'controller_id', 'action_id', 'permission', 'link', 'title'], 'required'],
            [['hotel_role_id', 'permission', 'type', 'parent_id'], 'integer'],
            [['module_id', 'controller_id', 'action_id'], 'string', 'max' => 20],
            [['link'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 10]
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
            'controller_id' => 'Controller ID',
            'action_id' => 'Action ID',
            'permission' => 'Permission',
            'type' => '类型1html，2json，3form提交',
            'link' => 'Link',
            'title' => 'Title',
            'parent_id' => '父模块id',
        ];
    }
}
