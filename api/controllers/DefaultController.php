<?php

namespace api\controllers;

use api\components\ProcessController;

class DefaultController extends ProcessController
{
    public function actionIndex()
    {
        return $this->responseText('谢谢');
    }
}
