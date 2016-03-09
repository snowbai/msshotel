<?php

namespace common\components;

use yii;
use yii\web\Controller;
use yii\web\Response;
use common\Medeen;
use yii\helpers\Json;

/**
 * 公共基类
 * author HDY.TT@medeen.com
 */
class CommonController extends Controller
{
    private $_moduleId;
    private $_controllerId;
    private $_actionId;

    public $defaultAction = 'index';
    //基类初始化
    public function init()
    {
        parent::init();
        //js css版本号
        defined('JS_CSS_VERSION') or define('JS_CSS_VERSION', Medeen::getAppParam('js_css_version',1));
        //前台http根路径
        defined('FRONT_ROOT') or define('FRONT_ROOT', Medeen::getAppParam('front_path','/data/image.medeen.com'));
        //前端文件http路径  供前端 js  css images uploads使用，正式环境会改为
        defined('FRONT_PUBLIC') or define('FRONT_PUBLIC', Medeen::getAppParam('imagecdn_base_url','http://imagecdn.medeen.com'));
        Medeen::setAlias("@msshost", Medeen::getAppParam('mss_base_url','http://mss.medeen.com'));
    }

    //提供ajax或者jsonp返回对象
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        if(true === Medeen::getRequest()->getIsAjax()||$callback = Medeen::getGetValue('callback'))
        {
            if($callback)
            {
                //return 'asdgasdg';
                Medeen::setResponseFormat(Response::FORMAT_JSONP);
                echo $callback.'('.Json::encode($result).')';
                return;
            }
            if(Medeen::getRequest()->getIsAjax())
            {
                Medeen::setResponseFormat(Response::FORMAT_JSON);
                echo  Json::encode($result);
                return;
            }
        }else{
            return $result;
        }
    }

    public function getRoute()
    {

        $this->setModlueId($this->module->id);
        $this->setControllerId($this->id);
        $this->setActionId($this->action->id);
    }

    protected function setModlueId($moduleId)
    {
        $this->_moduleId = $moduleId;
    }
    public function getModlueId()
    {
        return $this->_moduleId;
    }
    protected function setControllerId($controllerId)
    {
        $this->_controllerId = $controllerId;
    }
    public function getControllerId()
    {
        return $this->_controllerId;
    }
    protected function setActionId($actionId)
    {
        return $this->_actionId = $actionId;
    }
    public function getActionId()
    {
        return $this->_actionId;
    }

}
