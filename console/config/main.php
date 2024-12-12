<?php

use yii\db\Connection;
use yii\symfonymailer\Mailer;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
          ],
    ],
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
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
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
    'params' => $params,
];
