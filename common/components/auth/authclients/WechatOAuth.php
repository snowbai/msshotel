<?php

namespace common\components\auth\authclients;

use common\Medeen;
use common\components\mobileerror\AuthException;
use mss\components\MssMemberSeesion;
use mss\models\HotelmemberOauth;
use yii;
use yii\authclient\OAuth2;
use yii\base\Exception;
use yii\helpers\Json;
use callmez\wechat\sdk\MpWechat;
use common\components\MpApp;

/**
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'wechat' => [
 *                 'class' => 'common\components\WechatOAuth',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 *
 * @author huhonghui <huhonghui@medeen.com>
 * @since 2.0
 */
class WechatOAuth extends OAuth2
{
    public $authUrl = MpWechat::WECHAT_OAUTH2_AUTHORIZE_URL;
    public $tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    public $apiBaseUrl = MpWechat::WECHAT_BASE_URL;

    public $redirectSuccessUrl = '';
    public $redirectFailUrl = '';
    public function init()
    {
        parent::init();
        $mpapp = new Mpapp();
        $mpapp->getMpApp();
        $this->clientId = $mpapp->mp_appid;
        $this->clientSecret = $mpapp->mp_secret;
        $this->scope = 'snsapi_base';
    }
    /**
     * Composes user authorization URL.
     * @param array $params additional auth GET params.
     * @return string authorization URL.
     */
    public function buildAuthUrl(array $params = [])
    {

        $defaultParams = [
            'appid' => $this->clientId,
            'redirect_uri' => $this->getReturnUrl(),
            'response_type' => 'code',
            'scope' => $this->scope,
            'state' => $this->getredirectSuccessUrl(),
            '#wechat_redirect',
        ];
        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }

    protected function getredirectSuccessUrl()
    {
        return (Medeen::getGetValue('redirect')) ? Medeen::getGetValue('redirect') : $this->getReturnUrl();
    }
    /**
     * Gets new auth token to replace expired one.
     * @param OAuthToken $token expired auth token.
     * @return OAuthToken new auth token.
     */
    public function fetchAccessToken($authCode, array $params = [])
    {
        $defaultParams = [
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
            'code' => $authCode,
            'grant_type' => 'authorization_code',
        ];
        $response = $this->sendRequest('POST', $this->tokenUrl, array_merge($defaultParams, $params));
        $token = $this->createToken(['params' => $response]);
        //$this->setAccessToken($token);
        $this->checkToken($token);
        return $token;
    }

    protected function checkToken($token)
    {

        $member_oauth = HotelmemberOauth::ckeckToken($token);
        MssMemberSeesion::doOauth($member_oauth);
    }
    /**
     * Sets persistent state.
     * @param string $key state key.
     * @param mixed $value state value
     * @return $this the object itself
     */
    protected function setState($key, $value)
    {
        if (!Yii::$app->has('session')) {
            return $this;
        }
        /* @var \yii\web\Session $session */
        $session = Yii::$app->get('session');
        $key = $this->getStateKeyPrefix() . $key;
        $session->set($key, $value);
        return $this;
    }

    /**
     * Returns persistent state value.
     * @param string $key state key.
     * @return mixed state value.
     */
    protected function getState($key)
    {
        if (!Yii::$app->has('session')) {
            return null;
        }
        /* @var \yii\web\Session $session */
        $session = Yii::$app->get('session');
        $key = $this->getStateKeyPrefix() . $key;
        $value = $session->get($key);
        return $value;
    }

    /**
     * Removes persistent state value.
     * @param string $key state key.
     * @return boolean success.
     */
    protected function removeState($key)
    {
        if (!Yii::$app->has('session')) {
            return true;
        }
        /* @var \yii\web\Session $session */
        $session = Yii::$app->get('session');
        $key = $this->getStateKeyPrefix() . $key;
        $session->remove($key);
        return true;
    }
    protected function initUserAttributes()
    {

        return $wechat_user;
    }

    protected function defaultName()
    {
        return 'Wechat';
    }

    protected function defaultTitle()
    {
        return 'Wechat';
    }


    /**
     * 该扩展初始的处理方法似乎QQ互联不能用，应此改写了方法
     * @see \yii\authclient\BaseOAuth::processResponse()
     */
    protected function processResponse($rawResponse, $contentType = self::CONTENT_TYPE_AUTO)
    {
        if (empty($rawResponse)) {
            return [];
        }
        switch ($contentType) {
            case self::CONTENT_TYPE_AUTO: {
                $contentType = $this->determineContentTypeByRaw($rawResponse);
                if ($contentType == self::CONTENT_TYPE_AUTO) {
                    //以下代码是特别针对QQ互联登录的，也是与原方法不一样的地方
                    if(strpos($rawResponse, "callback") !== false){
                        $lpos = strpos($rawResponse, "(");
                        $rpos = strrpos($rawResponse, ")");
                        $rawResponse = substr($rawResponse, $lpos + 1, $rpos - $lpos -1);
                        $response = $this->processResponse($rawResponse, self::CONTENT_TYPE_JSON);
                        break;
                    }
                    //代码添加结束
                    throw new Exception('Unable to determine response content type automatically.');
                }
                $response = $this->processResponse($rawResponse, $contentType);
                break;
            }
            case self::CONTENT_TYPE_JSON: {
                $response = Json::decode($rawResponse, true);
                if (isset($response['error'])) {
                    throw new Exception('Response error: ' . $response['error']);
                }
                break;
            }
            case self::CONTENT_TYPE_URLENCODED: {
                $response = [];
                parse_str($rawResponse, $response);
                break;
            }
            case self::CONTENT_TYPE_XML: {
                $response = $this->convertXmlToArray($rawResponse);
                break;
            }
            default: {
                throw new Exception('Unknown response type "' . $contentType . '".');
            }
        }

        return $response;
    }


}
