<?php

namespace common\components;

use yii;
use yii\base\Component;

/**
 * description
 */
class MpApp
{
    public $mp_appid = '';
    public $mp_secret = '';
    public $mp_type = '';
    /**
     * [getMpApp 获取微信公众号秘钥type]
     * @method getMpApp
     * @return [array]   [appid,secret]
     */
    public function getMpApp()
    {
        $this->mp_appid = 'wx3c5297400cfa7270';
        $this->mp_secret = 'b3601121c7352edbc1680d16bc7f9174';
        return ['mp_appid' => $this->mp_appid,'mp_secret' => $this->mp_secret];
    }
}
