<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%mss_g_h_meta}}".
 *
 * @property string $meta_id
 * @property integer $g_id
 * @property integer $h_id
 * @property string $meta_key
 * @property string $meta_value
 */
class MssGHMeta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mss_g_h_meta}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['g_id', 'h_id'], 'integer'],
            [['meta_value'], 'string'],
            [['meta_key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'meta_id' => 'ID',
            'g_id' => '集团ID',
            'h_id' => '酒店ID',
            'meta_key' => '数据键名',
            'meta_value' => '数据键值',
        ];
    }
}
