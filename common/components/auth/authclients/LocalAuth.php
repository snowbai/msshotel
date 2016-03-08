<?php

namespace common\components\auth\authclients;

use common\Medeen;
use common\components\mobileerror\AuthException;
use mss\models\HotelmemberLocalauth;
use yii\authclient\BaseClient;
use yii\helpers\Json;
use yii\web\response;

/**
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'qq' => [
 *                 'class' => 'common\components\QqOAuth',
 *                 'clientId' => 'qq_client_id',
 *                 'clientSecret' => 'qq_client_secret',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 * @see http://connect.qq.com/
 *
 * @author easypao &lt;admin@easypao.com>
 * @since 2.0
 */
class LocalAuth extends BaseClient
{

    public function init()
    {
        parent::init();

    }
    /**
     * Composes default [[returnUrl]] value.
     * @return string return URL.
     */
    protected function defaultReturnUrl()
    {
        return Medeen::getRequest()->getAbsoluteUrl();
    }

    protected function initUserAttributes()
    {
    }

    protected function defaultName()
    {
        return 'LocalAuth';
    }

    protected function defaultTitle()
    {
        return 'LocalAuth';
    }
    /**
     * [auth description]
     * @method auth
     * @return [type] [description]
     */
    public function auth()
    {
        $member_phone = Medeen::getPostValue('member_phone');
        $member_pwd = md5(Medeen::getPostValue('member_pwd'));
        $type = Medeen::getPostValue('type');
        $g_id = (Medeen::getPostValue('g_id'))?Medeen::getPostValue('g_id'):Medeen::getGetValue('g_id');
        $h_id = (Medeen::getPostValue('h_id'))?Medeen::getPostValue('h_id'):Medeen::getGetValue('h_id');
        $member_phone = 15888023193;
        $member_pwd = md5(123456);
        $type = 1;
        $g_id = 1;
        $h_id = 1;

        try {
          return  HotelmemberLocalauth::auth($member_phone,$member_pwd,$type,$g_id,$h_id);

        } catch (AuthException $e) {
            Medeen::setResponseFormat(Response::FORMAT_JSON);
            echo Json::encode(array('isSuccess' =>  false,'error'=>array('code'=>$e->getCode(),'message'=>$e->getMessage())));
        }
    }

}
