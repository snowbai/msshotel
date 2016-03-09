<?php

namespace mdadmin\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%admin_permission}}".
 *
 * @property string $id
 * @property string $module_id
 * @property string $controller_id
 * @property string $action_id
 * @property integer $mode
 * @property integer $type
 * @property string $link
 * @property string $title
 * @property string $parent_id
 */
class AdminPermission extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_permission}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'controller_id', 'action_id', 'link', 'title'], 'required'],
            [['mode', 'type', 'parent_id'], 'integer'],
            [['module_id', 'controller_id', 'action_id'], 'string', 'max' => 20],
            [['link'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 10],
            [['module_id', 'controller_id', 'action_id'], 'unique', 'targetAttribute' => ['module_id', 'controller_id', 'action_id'], 'message' => 'The combination of Module ID, Controller ID and action_id has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'module_id',
            'module_id' => 'Module ID',
            'controller_id' => 'Controller ID',
            'action_id' => 'action_id',
            'mode' => '权限数值2的n次方',
            'type' => '类型1html，2json，3form提交',
            'link' => '链接地址',
            'title' => 'Title',
            'parent_id' => '父模块id',
        ];
    }
}
