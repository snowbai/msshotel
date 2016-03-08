<?php
namespace common\components;

use yii\base\Object;

class ActionResult extends Object
{

    public $controller;

    public $action;

    public $isExecuted = false;

    public $result;

    public function __toString()
    {
        return $this->result;
    }
}
