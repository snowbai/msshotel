<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'mss',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'mss\controllers',
    'bootstrap' => ['log'],

    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'Name' => 'mss_sessionid',
            'UseCookies' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => [
                '<srouce:wx_><contoller:\w+>/<action:\w+>' => 'test/index',
                '<contoller:\w+>/<action:\w+>' => '<contoller>/<action>',
            ],
        ],
        'authClientCollection' => [
                'class' => 'yii\authclient\Collection',
                'clients' => [
                    'google' => [
                        'class' => 'yii\authclient\clients\GoogleOpenId'
                    ],
                    'facebook' => [
                        'class' => 'yii\authclient\clients\Facebook',
                        'clientId' => 'facebook_client_id',
                        'clientSecret' => 'facebook_client_secret',
                    ],
                    'qq' => [
                        'class' => 'common\components\auth\authclients\QqOAuth',
                        'clientId' => 'qq_client_id',
                        'clientSecret' => 'qq_client_secret',
                    ],
                    'wechat' => [
                        'class' => 'common\components\auth\authclients\WechatOAuth',
                    ],
                    'local' =>  [
                        'class' => 'common\components\auth\authclients\LocalAuth',
                    ],
                ],
            ]
    ],
    'modules' => [
        'auth' => [
            'class' => 'mss\modules\auth\Module',
        ],
        'wechat' => [
            'class' => 'callmez\wechat\Module',
        ],
    ],
    'params' => $params,
];
