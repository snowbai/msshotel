<?php

namespace mdadmin\models\ar;

use Yii;
use common\Medeen;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%admin_user}}".
 *
 * @property string $adminuser_id
 * @property string $username
 * @property string $pwd
 * @property string $email
 * @property integer $mobile
 * @property integer $is_super
 * @property integer $created_at
 * @property integer $last_logintime
 */
class AdminUser extends \common\components\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'pwd', 'is_super', 'created_at', 'last_logintime'], 'required'],
            [['mobile', 'is_super', 'created_at', 'last_logintime'], 'integer'],
            [['username'], 'string', 'max' => 10],
            [['pwd'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'adminuser_id' => 'Adminuser ID',
            'username' => '用户名',
            'pwd' => 'Pwd',
            'email' => 'Email',
            'mobile' => 'Mobile',
            'role_id' => '角色id',
            'is_super' => '1超级管理员',
            'created_at' => 'Created At',
            'last_logintime' => 'Last Logintime',
        ];
    }

    public function behaviors()
    {
        return [

                'timestamp' => [
                    'class' => 'yii\behaviors\TimestampBehavior',
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ],
                ],

        ];
    }

    public static function login($admin_name,$admin_pwd){
        if(null === static::findOne(['username' => $admin_name]))
        {
            return [
                'isSuccess' => false,
                'error' => [
                    'code' => '101',
                    'message' => '用户名不正确',
                ]
            ];
        }

        $admin_user = static::findOne(['username' => $admin_name, 'pwd' => md5($admin_pwd)]);
        //echo $admin_name.'1111111111'.md5($admin_pwd);
        //var_dump($admin_user);exit;
        if($admin_user)
        {
            $admin_user->last_logintime = time();
            $admin_user->save();
            $session = Medeen::getApp()->get('session');
            $key = 'md_admin_user' ;
            $adminsession = base64_encode(Json::encode([
                'adminuser_id' => $admin_user->adminuser_id,
                'username' => $admin_user->username,
                'role_id' => $admin_user->role_id,
                'is_super' => $admin_user->is_super,
              ]));
            $session->set($key, $adminsession);
            return [
                'isSuccess' => true,
                'data' => [
                    'user' => $adminsession
                ],
            ];
        }else{
            return [
                'isSuccess' => false,
                'error' => [
                    'code' => '102',
                    'message' => '密码不正确',
                ]
            ];
        }
    }
}
