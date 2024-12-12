<?php

namespace frontend\widgets\banners;

use common\models\banner\BannerService;
use yii\base\Widget;

class BannersWidget extends Widget
{
    private array $banners = [];

    public function init()
    {
        $service = new BannerService();
        $service->setParams(['active' => 1]);
        $this->banners = $service->readForCatalog()['banners'];
        parent::init();
    }

    public function run(): string
    {
        return $this->render('index', [
            'list' => $this->banners,
        ]);
    }
}