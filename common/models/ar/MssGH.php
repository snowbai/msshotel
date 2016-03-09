<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%mss_g_h}}".
 *
 * @property integer $h_id
 * @property integer $g_id
 * @property integer $h_type
 * @property string $name
 * @property string $name_en
 * @property integer $star
 * @property string $brief
 * @property string $brief_en
 * @property string $intro
 * @property string $intro_en
 * @property string $phone_1
 * @property string $phone_2
 * @property string $fax_1
 * @property string $fax_2
 * @property string $weibo_id
 * @property string $weibo_name
 * @property string $wechat_id
 * @property string $wechat_name
 * @property string $alipay_id
 * @property string $alipay_name
 * @property string $address
 * @property string $address_en
 * @property string $longitude
 * @property string $latitude
 * @property integer $map_zoom
 * @property string $city_code
 * @property integer $wechat_dev_mode
 * @property integer $alipay_dev_mode
 * @property integer $status
 * @property string $modify_time
 * @property string $add_time
 */
class MssGH extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mss_g_h}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['g_id', 'h_type', 'star', 'map_zoom', 'wechat_dev_mode', 'alipay_dev_mode', 'status'], 'integer'],
            [['intro', 'intro_en'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['modify_time', 'add_time'], 'safe'],
            [['name', 'fax_1', 'fax_2', 'weibo_id', 'weibo_name', 'wechat_id', 'wechat_name', 'alipay_id', 'alipay_name'], 'string', 'max' => 32],
            [['name_en'], 'string', 'max' => 64],
            [['brief', 'address'], 'string', 'max' => 128],
            [['brief_en', 'address_en'], 'string', 'max' => 256],
            [['phone_1', 'phone_2'], 'string', 'max' => 20],
            [['city_code'], 'string', 'max' => 6]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'h_id' => 'ID，g_id=h_id单体酒店（或集团），g_id!=h_id非单体酒店',
            'g_id' => '集团ID',
            'h_type' => '信息类型',
            'name' => '名称',
            'name_en' => '名称（英文）',
            'star' => '星级',
            'brief' => '简介',
            'brief_en' => '简介（英文）',
            'intro' => '介绍',
            'intro_en' => '介绍（英文）',
            'phone_1' => '电话1',
            'phone_2' => '电话2',
            'fax_1' => '传真1',
            'fax_2' => '传真2',
            'weibo_id' => '微博号',
            'weibo_name' => '微博昵称',
            'wechat_id' => '微信号',
            'wechat_name' => '微信昵称',
            'alipay_id' => '支付宝服务号',
            'alipay_name' => '支付宝服务号昵称',
            'address' => '地址',
            'address_en' => '地址（英文）',
            'longitude' => '经度',
            'latitude' => '纬度',
            'map_zoom' => '缩放级别',
            'city_code' => '城市代号',
            'wechat_dev_mode' => '微信开发者模式，0 未开启',
            'alipay_dev_mode' => '支付宝开发这模式，0 未开启',
            'status' => '状态',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
