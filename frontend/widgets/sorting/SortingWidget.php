<?php

namespace frontend\widgets\sorting;

use yii\base\Widget;

class SortingWidget extends Widget
{
    public array $params = [];

    private array $sorting = [
        'popular-desc' => [
            'sort' => 'is_popular',
            'order' => 'DESC',
            'text' => 'Популярное',
        ],
        'new-desc' => [
            'sort' => 'date_created',
            'order' => 'DESC',
            'text' => 'По новизне',
        ],
        'price-desc' => [
            'sort' => 'price',
            'order' => 'DESC',
            'text' => 'По убыванию цены',
        ],
        'price-asc' => [
            'sort' => 'price',
            'order' => 'ASC',
            'text' => 'По возрастанию цены',
        ],
    ];

    private string $current = '';

    public function init()
    {
        if (isset($this->params['sort'])) {
            foreach ($this->sorting as $key => $sort) {
                if (
                    $sort['sort'] == $this->params['sort'] &&
                    $sort['order'] == ($this->params['order'] ?? 'ASC')
                ) {
                    $this->current = $key;
                }
            }
        }

        SortingAsset::register($this->view);
        parent::init();
    }

    public function run(): string
    {
        return $this->render('index', [
            'list' => $this->sorting,
            'current' => $this->current,
        ]);
    }
}