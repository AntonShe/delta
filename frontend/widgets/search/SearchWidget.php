<?php

namespace frontend\widgets\search;

use yii\base\Widget;

class SearchWidget extends Widget
{
    public function init()
    {
        SearchAsset::register($this->view);
        parent::init();
    }

    public function run(): string
    {
        return $this->render('index');
    }
}