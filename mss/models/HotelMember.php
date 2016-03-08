<?php

namespace mss\models;

use common\components\BaseActiveRecord;
use common\MdErrorInfo;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%hotel_member}}".
 *
 * @property string $member_id
 * @property string $g_id
 * @property integer $h_id
 * @property string $member_phone
 * @property string $member_realname
 * @property string $birthday
 * @property string $id_card
 * @property string $member_note
 * @property string $sex
 * @property string $email
 * @property string $lalance
 * @property string $is_credits
 * @property string $register_time
 * @property string $lastlogin_time
 * @property string $referer_id
 * @property integer $status
 */
class HotelMember extends BaseActiveRecord
{
    const STATUS_DELETED = 0;//已删除会员
    const STATUS_ACTIVE = 10;//正常会员
    const STATUS_FROZEN = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hotel_member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['g_id', 'h_id'], 'required'],
            [['g_id', 'h_id', ' referer_id'], 'integer'],
            [['birthday', 'register_time', 'lastlogin_time'], 'safe'],
            [['sex', 'is_credits'], 'string'],
            [['lalance'], 'number'],
            [['member_phone'], 'string', 'max' => 15],
            [['member_realname', 'id_card'], 'string', 'max' => 20],
            [['member_note'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'g_id' => 'G ID',
            'h_id' => 'H ID',
            'member_phone' => 'Member Phone',
            'member_realname' => '会员姓名',
            'birthday' => '会员生日',
            'id_card' => '身份证号',
            'member_note' => '备注',
            'sex' => '0未知，1男，2女',
            'email' => '邮箱',
            'lalance' => '余额',
            'is_credits' => 'Is Credits',
            'register_time' => '注册时间',
            'lastlogin_time' => '最后登录时间',
            'referer_id' => '推荐人id',
            'status' => '会员状态',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($member_id,$status = self::STATUS_ACTIVE)
    {
        return static::findOne(['member_id' => $member_id, 'status' => $status]);
    }

    /**
     * [getSexName 获取性别]
     * @method getSexName
     * @return [type]     [description]
     */
    public function getSexName()
    {
        if($this->sex == 1){
          return '男';
        } elseif ($this->sex == 2){
          return '女';
        } else {
          return '未知';
        }
    }
}
