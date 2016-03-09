<?php

namespace mdadmin\controllers;

use yii;
use mdadmin\components\MdAdminController;

class TestController extends MdAdminController
{
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
        return 1245;
    }

    public function actionLogin()
    {
        return 'xxxxxx';
    }
}
