<?php

namespace common\components\auth;

use common\Medeen;
use mss\components\MssMemberSeesion;
use yii;
use yii\authclient\AuthAction;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\Response;

/**
 * 微信网页授权于其他方式不同，重写
 */
class MdAuthAction extends AuthAction
{
    /**
     *
     * @method authLocalOauth
     * @param  [type]         $client [description]
     * @return [type]                 [description]
     */
    protected function authLocalAuth($client){

        $auth = $client->auth();
        var_dump($auth);
        if(!empty($auth)) {
            MssMemberSeesion::setLoginSession($auth);
            //$client->redirectSuccessUrl = $_GET['state'];
            return $this->authSuccess($client);
        }
    }
  /**
   * Performs OAuth2 auth flow.
   * @param OAuth2 $client auth client instance.
   * @return Response action response.
   * @throws \yii\base\Exception on failure.
   */
    protected function authOAuth2($client)
    {
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'access_denied') {
                // user denied error
                return $this->redirectCancel();
            }else{
                // request error
                if (isset($_GET['error_description'])) {
                    $errorMessage = $_GET['error_description'];
                } elseif (isset($_GET['error_message'])) {
                    $errorMessage = $_GET['error_message'];
                } else {
                    $errorMessage = http_build_query($_GET);
                }
                throw new Exception('Auth error: ' . $errorMessage);
            }
        }

        // Get the access_token and save them to the session.
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $token = $client->fetchAccessToken($code);

            if (!empty($token)) {
                $client->redirectSuccessUrl = $_GET['state'];
                return $this->authSuccess($client);
            } else {
                $client->redirectFailUrl = 'http://www.baidu.com';
                return $this->redirectCancel();
            }
        } else {
            $url = $client->buildAuthUrl();
            return Yii::$app->getResponse()->redirect($url);
        }
    }
    /**
     * Creates default [[successUrl]] value.
     * @return string success URL value.
     */
    protected function defaultSuccessUrl()
    {
        return (Medeen::getPostValue('redirect_uri')) ? Medeen::getPostValue('redirect_uri') : 'http://www.medeen.com/';
    }

    /**
     * Creates default [[cancelUrl]] value.
     * @return string cancel URL value.
     */
    protected function defaultCancelUrl()
    {
        return 'http://www.taobao.com';
    }
  /**
   * This method is invoked in case of successful authentication via auth client.
   * @param ClientInterface $client auth client instance.
   * @throws InvalidConfigException on invalid success callback.
   * @return Response response instance.
   */
    protected function authSuccess($client)
    {
        if (!is_callable($this->successCallback)){
            throw new InvalidConfigException('"' . get_class($this) .   '::successCallback" should be a valid callback.');
        }
        $response = call_user_func($this->successCallback, $client);
        if ($response instanceof Response){
            return $response;
        }
        //return $this->redirectSuccess();
    }
}
