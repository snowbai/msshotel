<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%md_wechat_article}}".
 *
 * @property string $id
 * @property integer $type
 * @property string $media_id
 * @property integer $wid
 * @property string $titile
 * @property string $thumb_media_id
 * @property string $thumb_url
 * @property integer $show_cover_pic
 * @property string $author
 * @property string $digest
 * @property string $content
 * @property string $url
 * @property string $content_source_url
 * @property string $update_time
 * @property string $add_time
 *
 * @property MdWechat $w
 */
class WechatArticle extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_article}}';
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
            [['type', 'wid', 'show_cover_pic'], 'integer'],
            [['wid', 'add_time'], 'required'],
            [['content'], 'string'],
            [['update_time', 'add_time'], 'safe'],
            [['media_id', 'thumb_media_id'], 'string', 'max' => 15],
            [['titile', 'author'], 'string', 'max' => 50],
            [['thumb_url', 'url', 'content_source_url'], 'string', 'max' => 255],
            [['digest'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'md_wechat表id',
            'type' => '素材类型 (1单图文，2多图文，0多图文子图文)',
            'media_id' => '素材id',
            'wid' => 'Wid',
            'titile' => '图文消息的标题',
            'thumb_media_id' => '图文消息的封面图片素材id（必须是永久mediaID）',
            'thumb_url' => '图文消息的封面图片的地址，第三方开发者也可以使用这个URL下载图片到自己服务器中，然后显示在自己网站上',
            'show_cover_pic' => '是否显示封面，0为false，即不显示，1为true，即显示',
            'author' => '作者',
            'digest' => 'Digest',
            'content' => '图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS',
            'url' => '图文页的URL，或者，当获取的列表是图片素材列表时，该字段是图片的URL',
            'content_source_url' => '图文消息的原文地址，即点击“阅读原文”后的URL',
            'update_time' => '更新时间',
            'add_time' => '添加记录时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getW()
    {
        return $this->hasOne(MdWechat::className(), ['id' => 'wid']);
    }
}
