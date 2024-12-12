<?php

namespace frontend\widgets\genres_on_main;

use common\models\genre\GenreService;
use yii\base\Widget;

class GenresOnMainWidget extends Widget
{
    public function run(): string
    {
        $service = new GenreService();
        $service->setParams(['onMain' => 1, 'isCourse' => 0]);

        return $this->render('index', [
            'list' => $service->getGenresByCatalog(),
        ]);
    }
}