<?php

namespace mssadmin\controllers;

use mssadmin\models\ar\MssadminUser;
use mssadmin\components\MssadminController;
use common\Medeen;
use yii\helpers\Json;
use yii\helpers\Url;

class LoginController extends MssadminController
{
    public $enableCsrfValidation = false;
    public $layout = false;

    public function actions()
    {
        return [
          'yzm' =>[
              'class' => 'yii\captcha\CaptchaAction',
            ],

        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        $admin_name = Medeen::getPostValue('username');
        $admin_pwd = Medeen::getPostValue('password');
        $verify_code = Medeen::getPostValue('verifycode');
        if(empty($admin_name)||empty($admin_pwd)||empty($verify_code)){
            return [
                'isSuccess' => false,
                'error' => [
                    'code' => 104,
                    'message' => '参数不能为空'
                ],
            ];
        }
        if(strtolower($verify_code) != strtolower($this->createAction('yzm')->getVerifyCode()))
        {
          return [
              'isSuccess' => false,
              'error' => [
                  'code' => 103,
                  'message' => '验证码不正确'
              ],
          ];
        }
        $admin = MssadminUser::login($admin_name, $admin_pwd);
        if(true === $admin['isSuccess'])
        {
            $userinfo = Json::decode(base64_decode($admin['data']['user']));
            $admin['data']['url'] = Url::to(['index/index','g_id'=>$userinfo['g_id'],'h_id' => $userinfo['h_id']]);
            return $admin;
        }
    }
}
