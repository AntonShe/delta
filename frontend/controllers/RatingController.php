<?php

namespace frontend\controllers;

use common\models\rating\RatingService;
use Yii;
use yii\web\Response;

class RatingController extends AbstractController
{
    protected RatingService $ratingService;

    public function __construct($id, $module, $config = [])
    {
        $this->ratingService = new RatingService();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): Response
    {
        if (empty($this->params)) {
            return $this->asJson(['errors' => 'Не переданы параметры']);
        }

        $this->ratingService->setParams($this->params);
        return $this->asJson($this->ratingService->getRatingByProductAndUser());
    }

    public function actionCreate(): Response
    {
        if (Yii::$app->user->isGuest) {
            return $this->asJson(['errors' => 'Только авторизованным']);
        }

        if (empty($this->data)) {
            return $this->asJson(['errors' => 'Не переданы параметры']);
        }

        $this->ratingService->setParams($this->data);
        return $this->asJson($this->ratingService->setRating());
    }
}
