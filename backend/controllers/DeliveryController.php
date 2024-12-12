<?php

namespace backend\controllers;

use common\models\delivery\DeliveryService;
use common\models\points\PointService;
use yii\web\Response;

class DeliveryController extends AbstractController
{
    protected DeliveryService $service;
    protected PointService $pointService;

    public function __construct($id, $module, $config = [])
    {
        $this->service = new DeliveryService();
        $this->pointService = new PointService();

        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return parent::behaviors();

        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionCalculate()
    {
        $this->service->setParams($this->data);

        return $this->service->calculate();
    }

    public function actionSuggest()
    {
        $this->service->setParams($this->data);

        return $this->service->getSuggest();
    }

    public function actionGetCityList()
    {
        return $this->pointService->getAllCities();
    }

    public function actionGetPoints()
    {
        return $this->pointService->getAllPoints();
    }
}