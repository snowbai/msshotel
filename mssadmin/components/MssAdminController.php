<?php

namespace mssadmin\components;

use yii;
use common\components\CommonController;
use common\Medeen;

/**
 * author HDY.TT
 * 酒店管理后台基类
 */
class MssadminController extends CommonController
{
    public $g_id ;
    public $h_id ;

    public $hotel_type;

    public function init()
    {
        $this->getRoute();
        if('login' != strtolower($this->getControllerId()))
        {
            
        }
    }

    public function beforeAction($action)
    {

    }

    public function createMssUrl($url, $scheme = false)
    {
        $keys = array_keys($url);
        if(!array_key_exists('g_id', $url)||!array_key_exists('h_id', $url)){

        }
    }

    public function createUrl($url, $scheme = false)
    {

    }
}
