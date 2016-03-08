<?php

namespace common\models;

use common\Medeen;
use Yii;
use callmez\wechat\sdk\MpWechat;

/**
 * This is the model class for table "{{%md_wechat}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $hash
 * @property string $token
 * @property string $access_token
 * @property string $account
 * @property string $original
 * @property integer $type
 * @property string $app_id
 * @property string $app_secret
 * @property integer $encoding_type
 * @property string $encoding_aes_key
 * @property integer $is_used
 * @property string $qr_code
 * @property string $created_at
 * @property string $updated_at
 *
 * @property WechatArticle[] $WechatArticles
 */
class Wechat extends \common\components\BaseActiveRecord
{
    /**
     * 未激活状态
     */
    const STATUS_INACTIVE = 0;
    /**
     * 激活状态
     */
    const STATUS_ACTIVE = 1;
    /**
     * 删除状态
     */
    const STATUS_DELETED = 2;
    /**
     * 普通订阅号
     */
    const TYPE_SUBSCRIBE = 2;
    /**
     * 认证订阅号
     */
    const TYPE_SUBSCRIBE_VERIFY = 2;
    /**
     * 普通服务号
     */
    const TYPE_SERVICE = 3;
    /**
     * 认证服务号
     */
    const TYPE_SERVICE_VERIFY = 4;
    /**
     * 公众号类型列表
     * @var array
    */
    public static $types = [
      self::TYPE_SUBSCRIBE => '订阅号',
      self::TYPE_SUBSCRIBE_VERIFY => '认证订阅号',
      self::TYPE_SERVICE => '服务号',
      self::TYPE_SERVICE_VERIFY => '认证服务号',
    ];
    public static $statuses = [
      self::STATUS_INACTIVE => '未接入',
      self::STATUS_ACTIVE => '已接入',
      self::STATUS_DELETED => '已删除'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%md_wechat}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Medeen::getApp()->get('db_wechat');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'encoding_type', 'is_used', 'created_at', 'updated_at'], 'integer'],
            [['name', 'original'], 'string', 'max' => 40],
            [['hash'], 'string', 'max' => 5],
            [['token'], 'string', 'max' => 32],
            [['access_token', 'qr_code'], 'string', 'max' => 255],
            [['account'], 'string', 'max' => 30],
            [['app_id', 'app_secret'], 'string', 'max' => 50],
            [['encoding_aes_key'], 'string', 'max' => 43],
            [['hash'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '公众号名称',
            'hash' => '公众号名称',
            'token' => '微信服务访问验证token',
            'access_token' => '访问微信服务验证token',
            'account' => '微信号',
            'original' => '原始ID',
            'type' => '公众号类型,订阅号,认证订阅号,服务号,认证服务号',
            'app_id' => 'AppID',
            'app_secret' => 'AppSecret',
            'encoding_type' => '消息加密方式,1明文2加密3兼容',
            'encoding_aes_key' => '消息加密秘钥EncodingAesKey',
            'is_used' => '使用状态，1在用，其他不用，用于是否定时刷新access_token',
            'qr_code' => '二维码地址',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWechatArticles()
    {
        return $this->hasMany(WechatArticle::className(), ['wid' => 'id']);
    }

    /**
     * @inheritdoc
     * @return WechatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WechatQuery(get_called_class());
    }
    /**
     * [$_sdk description]
     * @var [WechatSDK]
     */
    private $_sdk;

    public function getSdk()
    {

        if($this->_sdk === null) {
            unset($this->name);
            $this->_sdk = Yii::createObject(['class'=>MpWechat::className() , 'appId'=>$this->app_id,'id'=>$this->id,'appSecret'=>$this->app_secret,'token'=>$this->token,'encodingAesKey'=>$this->encoding_aes_key]);
        }
        return $this->_sdk;
    }
}
