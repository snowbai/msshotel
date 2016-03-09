<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%mss_g_h_menu}}".
 *
 * @property integer $menu_id
 * @property integer $parent_id
 * @property integer $g_id
 * @property integer $h_id
 * @property integer $menu_type
 * @property string $menu_name
 * @property string $menu_url
 * @property string $menu_img
 * @property integer $status
 * @property integer $list_order
 * @property string $modify_time
 * @property string $add_time
 */
class MssGHMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mss_g_h_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'g_id', 'h_id', 'menu_type', 'status', 'list_order'], 'integer'],
            [['modify_time', 'add_time'], 'safe'],
            [['menu_name'], 'string', 'max' => 32],
            [['menu_url', 'menu_img'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => 'ID',
            'parent_id' => '父ID',
            'g_id' => '集团id',
            'h_id' => '酒店id',
            'menu_type' => '菜单类型',
            'menu_name' => '菜单名称',
            'menu_url' => '菜单URL',
            'menu_img' => '菜单图片',
            'status' => '状态',
            'list_order' => '排序',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
