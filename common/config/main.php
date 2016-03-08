<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
          'class' => 'yii\db\Connection',
          'dsn' => 'mysql:host=localhost;dbname=medeen',
          'username' => 'root',
          'password' => '',
          'charset' => 'utf8',
          'tablePrefix' => 'md_',
        ],
        'db_wechat' => [
          'class' => 'yii\db\Connection',
          'dsn' => 'mysql:host=localhost;dbname=md_wechat',
          'username' => 'root',
          'password' => '',
          'charset' => 'utf8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'wechat_cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
