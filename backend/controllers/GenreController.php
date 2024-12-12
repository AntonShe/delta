<?php

namespace backend\controllers;

use common\models\genre\GenreService;
use Yii;

class GenreController extends AbstractController
{
    protected GenreService $genreService;

    public function __construct($id, $module, $config = [])
    {
        $this->genreService = new GenreService();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        $this->genreService->setParams($this->params);

        return $this->genreService->getGenres();
    }

    public function actionCreate(): array
    {
        if (!isset($this->data['name']) || !isset($this->data['parentId'])) {
            return ['errors' => 'Не переданы параметры'];
        }

        $this->genreService->setParams($this->data);
        return $this->genreService->createGenre();
    }

    public function actionUpdate(): array
    {
        if (!isset($this->params['id'])) {
            return ['errors' => ['Не передан id жанра']];
        }

        $this->genreService->setParams($this->data);
        return $this->genreService->updateGenre($this->params['id']);
    }

    public function actionDelete(): array
    {
        if (!isset($this->params['id'])) {
            return ['errors' => ['Не передан id жанра']];
        }

        return $this->genreService->deleteGenre($this->params['id']);
    }
}