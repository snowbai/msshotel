<?php

namespace mss\models;

use common\MdAuthErrorInfo;
use common\components\BaseActiveRecord;
use common\components\mobileerror\AuthException;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%hotelmember_oauth}}".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $oauth_name
 * @property string $oauth_openid
 * @property string $oauth_access_token
 * @property string $oauth_expires
 * @property string $oauth_refresh_token
 */
class HotelmemberOauth extends BaseActiveRecord
{
    const TYPE_OAUTH_WECHAT = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotelmember_oauth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'oauth_name'], 'integer'],
            [['oauth_access_token'], 'required'],
            [['oauth_expires'], 'safe'],
            [['oauth_openid', 'oauth_access_token', 'oauth_refresh_token'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'member_id' => '会员id',
            'oauth_name' => 'oauth2.0认证来源（1，微信，2，微博，3，支付宝，4,qq)',
            'oauth_openid' => 'Oauth Openid',
            'oauth_access_token' => '授权access_token',
            'oauth_expires' => '授权有效期',
            'oauth_refresh_token' => '授权刷新access_token',
            'is_auto' => '是否自动登录,1表示绑定后自动登录，2表示不登录，3表示解绑',
            'is_mss' => '1mss,2联盟',
        ];
    }
    /**
     * [auth description]
     * @method auth
     * @param  [type] $member_phone [description]
     * @param  [type] $member_pwd   [description]
     * @return [type]               [member_id]
     */
    public static function auth($oauth_openid)
    {
        if(static::hasOne(['oauth_openid' => $oauth_openid])){
            throw new AuthException("错误的openid", MdAuthErrorInfo::HOTEL_MEMBER_OAUTH_WRONGOPENID);
        }
        if(static::hasOne(['oauth_openid' => $oauth_openid, 'oauth_expires<now()'])){
            $authSuccess =  static::findOne(['oauth_openid' => $oauth_openid, 'member_pwd' =>$member_pwd]);
            if(!empty($authSuccess->member_id)){
                return $authSuccess->member_id;
            }else{
                throw new AuthException("未绑定账号", MdAuthErrorInfo::HOTEL_MEMBER_OAUTH_NOMEMBER);
            }
        }else{
            throw new AuthException("token失效", MdAuthErrorInfo::HOTEL_MEMBER_OAUTH_TOKENVALID);

        }
    }

    public static function ckeckToken(\yii\authclient\OAuthToken $token, $oauth_name = self::TYPE_OAUTH_WECHAT, $ismss = 1)
    {
        $createTimestamp = $token->createTimestamp;
        $param = $token->getParams();
        $openid = $param['openid'];
        $access_token = $param['access_token'];
        $refresh_token = $param['refresh_token'];
      //  $oauth_name = static::TYPE_OAUTH_WECHAT;
        $oauth_expires = $createTimestamp+$param['expires_in'];
        if($hotel_member_oauth = static::findOne(['oauth_openid' => $openid,'is_mss' => "$ismss"] ))
        {
            $hotel_member_oauth->oauth_access_token = $access_token;
            $hotel_member_oauth->oauth_refresh_token = $refresh_token;
            $hotel_member_oauth->oauth_expires = $oauth_expires;
        } else{
            $hotel_member_oauth = new HotelmemberOauth();
            $hotel_member_oauth->oauth_openid = $openid;
            $hotel_member_oauth->oauth_name = $oauth_name;
            $hotel_member_oauth->oauth_access_token = $access_token;
            $hotel_member_oauth->oauth_refresh_token = $refresh_token;
            $hotel_member_oauth->oauth_expires = $oauth_expires;
            $hotel_member_oauth->is_auto_login = 1;
            $hotel_member_oauth->is_mss = $ismss;
        }
        $return = $hotel_member_oauth->save();
        return $hotel_member_oauth;
    }
}
