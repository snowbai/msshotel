<?php

namespace mss\models;

use common\MdAuthErrorInfo;
use common\components\BaseActiveRecord;
use common\components\mobileerror\AuthException;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%hotelmember_localauth}}".
 *
 * @property string $id
 * @property string $member_id
 * @property string $member_phone
 * @property string $member_pwd
 * @property string $g_id
 * @property string $h_id
 */
class HotelmemberLocalauth extends BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotelmember_localauth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'member_phone', 'member_pwd', 'g_id', 'h_id'], 'required'],
            [['member_id', 'g_id', 'h_id'], 'integer'],
            [['member_phone'], 'string', 'max' => 15],
            [['member_pwd'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'member_id' => 'Member ID',
            'member_phone' => '手机号',
            'member_pwd' => '密码',
            'g_id' => 'G ID',
            'h_id' => 'H ID',
        ];
    }

    /**
     * [auth description]
     * @method auth
     * @param  [type] $member_phone [description]
     * @param  [type] $member_pwd   [description]
     * @return [type]               [member_id]
     */
    public static function auth($member_phone,$member_pwd,$type,$g_id,$h_id)
    {
        if($g_id != $h_id){

        }else{
            if(!static::findOne(['member_phone' => $member_phone,'g_id' => $g_id])){
                throw new AuthException("用户手机号不存在", MdAuthErrorInfo::HOTEL_MEMBER_LOCALAUTH_WRONGPHONE);
            }
            if($authSuccess = static::findOne(['member_phone' => $member_phone, 'g_id' => $g_id, 'member_pwd' =>$member_pwd])){
                return $authSuccess;
            }else{
                throw new AuthException("用户名或者密码错误", MdAuthErrorInfo::HOTEL_MEMBER_LOCALAUTH_WRONGPWD);
            }
        }

    }
}
