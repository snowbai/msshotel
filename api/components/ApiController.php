<?php

namespace api\components;

use common\Medeen;
use common\components\CommonController;
use yii;
use yii\helpers\Url;
use yii\web\Response;
use common\models\Wechat;

/**
 * author HDY.TT
 */
abstract class ApiController extends CommonController
{
    /**
     * 设置当前应用的公众号
     * @param Wechat $wechat
     * @return mixed
     */
    abstract public function setWechat(Wechat $wechat);

    /**
     * 获取当前应用的公众号
     * @return mixed
     */
    abstract public function getWechat();

}
