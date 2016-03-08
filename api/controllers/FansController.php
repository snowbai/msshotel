<?php

namespace api\controllers;

use api\components\ProcessController;
use Yii;

/**
 * author HDY.TT
 * description WechatFansController
 */
class FansController extends ProcessController
{

    public function actionSubscribe()
    {
        $this->responseText('你好，傻b！');
    }

    public function actionUnsubscribe()
    {
        $this->responseText('2b');
    }
}
