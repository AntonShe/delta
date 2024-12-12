<?php

namespace frontend\widgets\footer_menu;

use yii\base\Widget;

class FooterMenuWidget extends Widget
{
    const MENU_LIST = [
        'Услуги' => [
            'О компании' => '/contacts',
            'Акции' => '/promotion',
            'Доставка' => '/delivery',
            'Оплата&nbsp;и&nbsp;возврат' => '/payment-refund',
        ],
        'Другое' => [
            'Пользовательское соглашение' => '/user-agreement',
            'Публичная оферта для физ. лиц' => '/public-offer-person',
            'Публичная оферта для юр. лиц' => '/public-offer',
        ],
    ];

    public function run(): string
    {
        return $this->render('index', [
            'list' => self::MENU_LIST,
        ]);
    }
}