<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%ext_module}}".
 *
 * @property string $id
 * @property integer $type
 * @property integer $subtype
 * @property string $icon
 * @property integer $g_id
 * @property integer $h_id
 * @property string $name
 * @property string $brief
 * @property string $intro
 * @property string $open_start_time
 * @property string $open_end_time
 * @property string $book_phone1
 * @property string $book_phone2
 * @property string $extension
 * @property integer $book_type
 * @property integer $list_order
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class ExtModule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ext_module}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'subtype', 'g_id', 'h_id', 'book_type', 'list_order', 'status'], 'integer'],
            [['intro', 'extension'], 'string'],
            [['open_start_time', 'open_end_time', 'modify_time', 'add_time'], 'safe'],
            [['icon', 'brief'], 'string', 'max' => 256],
            [['name'], 'string', 'max' => 64],
            [['book_phone1', 'book_phone2'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'subtype' => '类型',
            'icon' => '小图标',
            'g_id' => '集团id',
            'h_id' => '门店id',
            'name' => '名称',
            'brief' => '简介',
            'intro' => '介绍',
            'open_start_time' => '开放开始时间',
            'open_end_time' => '开放结束时间',
            'book_phone1' => '预订电话一',
            'book_phone2' => '预订电话二',
            'extension' => '扩展值',
            'book_type' => '预定类型',
            'list_order' => '排序值',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
