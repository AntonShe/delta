<?php

use yii\caching\FileCache;
use yii\db\Connection;
use yii\symfonymailer\Mailer;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php'
);

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => $params['db_dsn'],
            'username' => $params['db_username'],
            'password' => $params['db_password'],
            'charset' => 'utf8mb3',
            'attributes' => [],
        ],
        'db2' => [
            'class' => Connection::class,
            'dsn' => $params['db2_dsn'],
            'username' => $params['db2_username'],
            'password' => $params['db2_password'],
            'charset' => 'utf8mb3',
            'attributes' => [],
        ],
        'db3' => [
            'class' => Connection::class,
            'schemaMap' => [
                'mysql' => SamIT\Yii2\MariaDb\Schema::class
            ],
            'dsn' => $params['db3_dsn'],
            'username' => $params['db3_username'],
            'password' => $params['db3_password'],
            'charset' => 'utf8mb3',
            'attributes' => [],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'mailer' => [
            'class' => Mailer::class,
            'transport' => [
                'dsn' => '',
            ],
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
        ],
    ],
];
