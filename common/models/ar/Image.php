<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%image}}".
 *
 * @property string $image_id
 * @property string $cat_type
 * @property integer $cat_id
 * @property integer $type
 * @property integer $subtype
 * @property integer $seq
 * @property string $name
 * @property string $url
 * @property string $path
 * @property string $update_time
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'type', 'subtype', 'seq'], 'integer'],
            [['update_time'], 'safe'],
            [['cat_type'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 32],
            [['url', 'path'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'image_id' => 'Image ID',
            'cat_type' => 'Cat Type',
            'cat_id' => 'Cat ID',
            'type' => 'Type',
            'subtype' => 'Subtype',
            'seq' => 'Seq',
            'name' => 'Name',
            'url' => 'Url',
            'path' => 'Path',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * 修改更新时间
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->update_time = date('Y-m-d H:i:s');
        return parent::beforeSave($insert);
    }
}
