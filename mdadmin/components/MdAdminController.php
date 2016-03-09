<?php

namespace mdadmin\components;

use yii;
use common\components\CommonController;
use common\Medeen;

class MdAdminController extends CommonController
{

    public function init()
    {
        parent::init();
        //$this->checkLogin();
        return true;
    }

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        $this->getRoute();
        $session = Medeen::getApp()->get('session');
        return (null != $session->get('md_admin_user')||'login' == strtolower($this->getControllerId())) ? true : $this->goHome();
    }
    /**
     * [createUrl description]
     * @method createUrl
     * @param  [type]    $route  [description]
     * @param  [type]    $scheme [description]
     * @return [type]            [description]
     * example $route ['/xx/xx/xx','id'=>1,'name'=>'123']
     */
    public function createUrl($route,$scheme = false){
        if($route === '')
        {
            $url = $this->getControllerId().'/'.$this->getActionId();
        }elseif(false === strpos($route, '/'))
        {
            $url = $this->getControllerId().'/'.$route;
        }
        $routeTmp = explode('/', trim($route, '/'));
        $key = count($routeTmp);
        if($key == 2)
        {
            $url = trim($route, '/');
        }elseif($key == 3)
        {
            $url = trim($route, '/');
        }
        return Url::to([$url,]);
    }

}
