<?php

namespace frontend\widgets\header_menu;

use common\models\genre\GenreService;
use yii\base\Widget;

class HeaderMenuWidget extends Widget
{
    const PROMOTION_MENU_URL = '/promotion';
    const DELIVERY_MENU_URL = '/delivery';
    const MAIN_LEVEL_MENU_LIST = [
        'Каталог' => '',
        'Акции' => self::PROMOTION_MENU_URL,
        'Доставка' => self::DELIVERY_MENU_URL,
    ];

    public function init()
    {
        HeaderMenuAsset::register($this->view);

        parent::init();
    }

    public function renderList(): string
    {
        return $this->render('list', [
            'list' => self::MAIN_LEVEL_MENU_LIST,
        ]);
    }

    public function renderDropdown(): string
    {
        $service = new GenreService();
        $service->setParams(['level' => [1, 2, 3], 'buildTree' => true]);

        return $this->render('dropdown', [
            'list' => self::MAIN_LEVEL_MENU_LIST,
            'genresList' => $this->formalGenresList($service->getGenres()),
            ]);
    }

    private function formalGenresList($list): array
    {
        $output = [];

        foreach ($list as $key => $item) {
            if ($item['level'] > 2) {
                continue;
            }
            $data = [
                'name' => $item['name'],
                'link' => "/catalog/{$item['id']}",
                'isEnd' => $key == (count($list) - 1)
            ];


            if (isset($item['children'])) {
                $data['children'] = $this->formalGenresList($item['children']);
            }

            $output[] = $data;
        }

        return $output;
    }
}