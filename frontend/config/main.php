<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => $params['db_dsn'],
            'username' => $params['db_username'],
            'password' => $params['db_password'],
            'charset' => 'utf8mb3',
            'attributes' => [],
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
            'cookieValidationKey' => 'g6jFfi3K1HdPDIRykcVsUy8iDuYDQejt',
            'parsers' => [
                'application/json' => \yii\web\JsonParser::class,
            ]
        ],
        'user' => [
            'class' => common\models\user\UserService::class,
            'identityClass' => 'common\models\user\UserRepository',
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'enableAutoLogin' => true,
        ],
        'session' => [
            'class' => 'yii\web\CacheSession',
            'name' => 'advanced-frontend',
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
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => null,
                'normalizeTrailingSlash' => true,
                'collapseSlashes' => true,
            ],
            'rules' => [
                //region rest
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        '' => 'site',
                        'cart' => 'cart',
                        'customer' => 'user',
                        'rating' => 'rating',
                        'genre' => 'genre',
                        'catalog' => 'catalog',
                        'product' => 'product',
                        'order' => 'order',
                        'favorite' => 'favorite',
                        'promotion' => 'promotion',
                        'person' => 'person',
                        'publisher' => 'publishing-house',
                        'series' => 'series',
                        'set' => 'set'
                    ],
                    'patterns' => [
                        'GET' => 'index',
                        'GET <id:\d+>' => 'index',
                        'GET ajax/<id:\d+>' => 'ajax',
                        'GET cart' => 'cart',
                        'GET full' => 'get-full',
                        'PATCH quantity' => 'set-quantity',
                        'DELETE' => 'delete',
                        'DELETE delete' => 'delete-items',
                        'GET badge' => 'get-user-badge',
                        'PATCH ' => 'update-user',
                        'POST' => 'create',
                    ]
                ],
                //endregion
                //region profile
                [
                    'pattern' => 'profile/info',
                    'route' => 'site/profile',
                ],
                [
                    'pattern' => 'profile/orders',
                    'route' => 'site/profile',
                ],
                [
                    'pattern' => 'profile/orders/<orderNumber:\d+>',
                    'route' => 'site/profile',
                ],
                [
                    'pattern' => 'profile/orders/<orderNumber:\d*\D+\d*>',
                    'route' => 'site/index',
                ],
                [
                    'pattern' => 'profile/balance',
                    'route' => 'site/profile',
                ],
                [
                    'pattern' => 'profile/favourite',
                    'route' => 'site/profile',
                ],
                [
                    'pattern' => 'search/search-line/list',
                    'route' => 'search/search-line',
                ],
                [
                    'pattern' => 'search/<search:[\d\w\s\!\'\.\,\-]+>',
                    'route' => 'search/search',
                ],
                [
                    'pattern' => 'search/ajax/<search:[\d\w\s\!\'\.\,\-]+>',
                    'route' => 'search/search-ajax',
                ],
                //endregion
                //region Redirects dog-nail
                [
                    'pattern' => 'catalog/<code:[\d\w\-]+>',
                    'route' => 'redirect/catalog',
                ],
                [
                    'pattern' => 'catalog/<code:[\d\w\-]+>/<dogNail:[\d\w\-]*>',
                    'route' => 'redirect/catalog',
                ],
                [
                    'pattern' => 'person/<code:[\d\w\-]+>',
                    'route' => 'redirect/person',
                ],
                [
                    'pattern' => 'publisher/<code:[\d\w\-]+>',
                    'route' => 'redirect/publishing-house',
                ],
                [
                    'pattern' => 'series/<code:[\d\w\-]+>',
                    'route' => 'redirect/series',
                ],
                [
                    'pattern' => 'b/<code:[\d\w\-]+>',
                    'route' => 'redirect/product',
                ],
                //endregion
                //region default
                '<action:[\w,-]+>' => 'site/<action>',
                '<controller:[\w,-]+>/<action:[\w,-]+>' => '<controller>/<action>',
                //endregion
            ],
        ],
    ],
    'params' => $params,
];
