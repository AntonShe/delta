<?php

namespace frontend\controllers;

use common\models\genre\GenreService;
use yii\web\Response;

class GenreController extends AbstractController
{
    protected GenreService $genreService;

    public function __construct($id, $module, $config = [])
    {
        $this->genreService = new GenreService();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): Response
    {
        if (empty($this->params)) {
            return $this->asJson(['errors' => 'Не переданы параметры']);
        }

        $this->genreService->setParams($this->params);

        return $this->asJson($this->genreService->getGenres());
    }
}
