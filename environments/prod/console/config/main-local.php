<?php

use yii\symfonymailer\Mailer;

return [
    'components' => [
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
