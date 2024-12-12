<?php

namespace frontend\widgets\pagination;

use yii\base\Widget;

class PaginationWidget extends Widget
{
    public int $pageCount = 1;
    public int $currentPage = 1;
    public int $startPage = 1;
    public int $endPage = 1;

    public function init()
    {
        PaginationAsset::register($this->view);

        parent::init();
    }

    public function run(): string
    {
        return $this->render('index', [
            'pageCount' => $this->pageCount,
            'currentPage' => $this->currentPage,
            'startPage' => $this->startPage,
            'endPage' => $this->endPage,
        ]);
    }
}