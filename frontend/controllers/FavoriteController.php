<?php

namespace frontend\controllers;

use Yii;
use common\models\favorite\FavoriteService;
use frontend\controllers\AbstractController;
use yii\web\Response;

class FavoriteController extends AbstractController
{
    protected FavoriteService $service;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->service = new FavoriteService();
    }

    public function behaviors()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return parent::behaviors();
    }

    public function actionIndex()
    {
        return $this->service->getFavorite();
    }

    public function actionGetFull()
    {
        return $this->service->getFavoriteProductFull();
    }

    public function actionCreate()
    {
        $this->service->setParams($this->data);

        return ['status' => $this->service->addFavorite()];
    }

    public function actionDelete()
    {
        $this->service->setParams($this->data);

        return ['status' => $this->service->deleteFavorite()];
    }


}