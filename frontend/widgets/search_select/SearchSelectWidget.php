<?php

namespace frontend\widgets\search_select;

use yii\base\Widget;

class SearchSelectWidget extends Widget
{
    protected string $subClasses;

    public string $id;
    public array $items;
    public array $additionalClassesList = [];
    public string $placeholder = '';

    public function init()
    {
        parent::init();
        SearchSelectAsset::register($this->view);

        $this->subClasses = empty($this->additionalClassesList)
            ? ''
            : implode(' ', $this->additionalClassesList);
    }

    public function run()
    {
        return $this->render('index', [
            'id' => $this->id,
            'items' => $this->items,
            'placeholder' => $this->placeholder,
            'additionalClasses' => $this->subClasses,
        ]);
    }
}