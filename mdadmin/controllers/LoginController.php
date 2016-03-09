<?php

namespace mdadmin\controllers;

use mdadmin\models\ar\AdminUser;
use mdadmin\components\MdAdminController;
use common\Medeen;

/**
 * author HDY.TT
 */
class LoginController extends MdAdminController
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
        return $this->render('login');
    }
    /**
     * [actionLogin description]
     * @method actionLogin
     * @return [type]      [description]
     */
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
        return AdminUser::login($admin_name, $admin_pwd);
    }

    public function actionLogout()
    {
        $session = Medeen::getApp()->get('session');
        $session->remove('md_admin_user');
        return $this->redirect('/login/index');
    }
}
