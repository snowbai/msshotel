<?php

namespace mss\modules\auth\controllers;

use mss\components\MssController;
use yii;
class MssauthController extends MssController
{
    public $layout = false;
    public function actions()
    {
      return [
            'auth' => [
                'class' => 'common\components\auth\MdAuthAction',
                'successCallback' => [$this, 'successCallback'],
            ],
        ];
    }
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function successCallback($client)
    {
        switch ($client->id) {
            case 'local':
                return 1;
            break;
            case 'wechat':
                return 2;
            break;

            default :
                return 3;
            break;
        }
    }
}
