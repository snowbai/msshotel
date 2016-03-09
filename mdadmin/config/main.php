<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'mdadmin',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'mdadmin\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
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
        'session' => [
            'class' => 'yii\web\Session',
            'Name' => 'mdadmin_sessionid',
            'UseCookies' => true,
        ],
        'urlManager' => [
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => [
                '<srouce:wx_><contoller:\w+>/<action:\w+>' => 'test/index',
                '<contoller:\w+>/<action:\w+>' => '<contoller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];
