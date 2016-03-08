<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%md_wechat_code}}".
 *
 * @property string $id
 * @property integer $wid
 * @property integer $scene_id
 * @property string $time_out
 * @property string $name
 * @property string $input
 * @property string $image
 * @property string $stat
 * @property integer $type
 * @property string $add_time
 */
class WechatCode extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_code}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_wechat');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wid', 'scene_id', 'time_out', 'stat', 'type'], 'integer'],
            [['input'], 'required'],
            [['input'], 'string'],
            [['add_time'], 'safe'],
            [['name', 'image'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wid' => '微信id对应wechat表中',
            'scene_id' => '场景值ID',
            'time_out' => '秒数限制，0永久',
            'name' => '名称',
            'input' => '输入数据，例如WiFi密码，积分数量等',
            'image' => '二维码地址',
            'stat' => '二维码扫描次数',
            'type' => '1wifi密码，2积分数...',
            'add_time' => '添加时间',
        ];
    }
}
