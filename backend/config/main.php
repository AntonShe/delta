<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl' => '/admin',
            'cookieValidationKey' => '4pMRHSuESV1wrSTqoTaCeeeJYM7ERaII',
            'parsers' => [
                'application/json' => \yii\web\JsonParser::class,
            ]
        ],
        'user' => [
            'class' => common\models\user\UserService::class,
            'identityClass' => 'common\models\user\UserRepository',
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'enableAutoLogin' => true,
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'session' => [
            'class' => 'yii\web\CacheSession',
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'prefix' => 'backend',
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'product' => 'product',
                        'user' => 'user',
                        'author' => 'author',
                        'genre' => 'genre',
                        'publishing-house' => 'publishing-house',
                        'age' => 'age',
                        'language' => 'language',
                        'level' => 'level',
                        'role' => 'role',
                        'banner' => 'banner',
                        'shelf' => 'shelf',
                        'promotion' => 'promotion',
                        'person' => 'person',
                        'publisher' => 'publishing-house',
                    //endregion
                    //region delivery
                        'courier' => 'delivery',
                        'pvz' => 'delivery',
                    //endregion
                    //region delivery
                        'order' => 'order',
                    //endregion
                    ],
                    'patterns' => [
                        'POST' => 'create',
                        'PATCH' => 'update',
                        'PATCH send/<id:\d+>' => 'send',
                        'DELETE' => 'delete',
                        'DELETE <id:\d+>' => 'delete',
                        'GET' => 'index',
                        'GET city' => 'get-city-list',
                        'GET full' => 'get-order-full',
                        'GET points' => 'get-points',
                        'GET <id:\d+>' => 'index',
                        'GET <page:\d+>' => 'index',
                        'POST login' => 'auth',
                        'POST calculate' => 'calculate',
                        'POST suggest' => 'suggest',
                    ]
                ],
                '/' => 'admin/index',
                '<controller:[\w,-]+>/' => 'admin/<controller>',
            ],
        ],
        'urlManagerFrontEnd' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => 'http://localhost:6660/',//TODO сделать проверку на localhost и перенести в $params
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'on beforeRequest' => ['common\models\api\FixPatchRequest', 'parseRequest'],
    'params' => $params,
];
