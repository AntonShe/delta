<?php

namespace frontend\widgets\breadcrumbs;

use common\models\product\ProductDecorator;
use common\models\product\ProductDTO;
use yii\base\Widget;

class BreadcrumbsWidget extends Widget
{
    const BREADCRUMBS_TYPE_PRODUCT = 'product';
    const BREADCRUMBS_TYPE_CATALOG = 'catalog';
    const BREADCRUMBS_TYPE_CATALOG_PRODUCTS = 'catalogProducts';
    const BREADCRUMBS_TYPE_CATALOG_GENRES = 'catalogGenres';
    const BREADCRUMBS_TYPE_GENRE = 'genre';
    const BREADCRUMBS_TYPE_PAGE = 'page';
    const BREADCRUMBS_TYPE_SEARCH = 'search';

    public string $template;
    public string $title = '';
    public int $level;
    public ?array $genre;
    public ?array $parentGenre;
    public ?ProductDecorator $product;

    public function init()
    {
        parent::init();
    }

    public function run(): string
    {
        $method = "getItems" . ucfirst($this->template);

        return $this->render('index', [
            'list' => $this->$method(),
        ]);
    }

    private function getItemsCatalogProducts(): array
    {
        if ($this->level == 1) {
            return [
                [
                    'title' => 'Главная',
                    'link' => '/',
                    'isActive' => false
                ],
                [
                    'title' => 'Каталог',
                    'link' => '',
                    'isActive' => false
                ],
                [
                    'title' => $this->title,
                    'link' => '',
                    'isActive' => true
                ],
            ];
        }

        $language = $this->product->genres[0]['name'];
        $languageId = $this->product->genres[0]['id'];

        return [
            [
                'title' => 'Главная',
                'link' => '/',
                'isActive' => false
            ],
            [
                'title' => 'Каталог',
                'link' => '',
                'isActive' => false
            ],
            [
                'title' => $language,
                'link' => "/catalog/{$languageId}",
                'isActive' => false
            ],
            [
                'title' => $this->title,
                'link' => '',
                'isActive' => true
            ],
        ];
    }

    private function getItemsCatalogGenres(): array
    {
        return [
            [
                'title' => 'Главная',
                'link' => '/',
                'isActive' => false
            ],
            [
                'title' => 'Каталог',
                'link' => '',
                'isActive' => false
            ],
            [
                'title' => $this->title,
                'link' => '',
                'isActive' => true
            ],
        ];
    }

    private function getItemsGenre(): array
    {
        return [
            [
                'title' => 'Главная',
                'link' => '/',
                'isActive' => false
            ],
            [
                'title' => 'Каталог',
                'link' => '',
                'isActive' => false
            ],
            [
                'title' => $this->parentGenre['name'],
                'link' => "/catalog/{$this->parentGenre['id']}",
                'isActive' => true
            ],
            [
                'title' => $this->title,
                'link' => '',
                'isActive' => true
            ],
        ];
    }

    private function getItemsProduct(): array
    {
        return [
            [
                'title' => 'Главная',
                'link' => '/',
                'isActive' => false
            ],
            [
                'title' => 'Каталог',
                'link' => '',
                'isActive' => false
            ],
            [
                'title' => $this->genre['name'],
                'link' => "/catalog/{$this->genre['id']}",
                'isActive' => false
            ],
            [
                'title' => $this->title,
                'link' => '',
                'isActive' => true
            ],
        ];
    }

    private function getItemsPage(): array
    {
        return [
            [
                'title' => 'Главная',
                'link' => '/',
                'isActive' => false
            ],
            [
                'title' => $this->title,
                'link' => '',
                'isActive' => true
            ],
        ];
    }

    private function getItemsSearch(): array
    {
        return [
            [
                'title' => 'Главная',
                'link' => '/',
                'isActive' => false
            ],
            [
                'title' => 'Поиск',
                'link' => '',
                'isActive' => true
            ],
        ];
    }
}