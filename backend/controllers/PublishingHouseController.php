<?php

namespace backend\controllers;

use common\models\publishing_house\PublishingHouseService;
use Yii;

class PublishingHouseController extends AbstractController
{
    protected PublishingHouseService $publishingHouseService;

    public function __construct($id, $module, $config = [])
    {
        $this->publishingHouseService = new PublishingHouseService();

        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return parent::behaviors();

        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionIndex(): array
    {
        $this->publishingHouseService->setParams($this->params);

        return [
            'data' => $this->publishingHouseService->getPublishingHouses()
        ];
    }

    public function actionCreate(): array
    {
        return [
            'status' => (int)$this->publishingHouseService->create($this->data)
        ];
    }

    public function actionUpdate(): array
    {
        return [
            'status' => (int)$this->publishingHouseService->update($this->data)
        ];
    }
}