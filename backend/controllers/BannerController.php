<?php

namespace backend\controllers;

use common\models\banner\BannerService;

class BannerController extends AbstractController
{
    private BannerService $service;

    public function __construct($id, $module, $config = [])
    {
        $this->service = new BannerService();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        $this->service->setParams($this->params);
        return $this->service->read();
    }

    public function actionCreate(): array
    {
        $this->service->setParams($this->data);
        return $this->service->create();
    }

    public function actionUpdate(): array
    {
        if (!isset($this->params['id'])) {
            return ['errors' => ['не передан id']];
        }

        $this->service->setParams($this->data);
        return $this->service->update($this->params['id']);
    }

    public function actionDelete(): array
    {
        if (!isset($this->params['id'])) {
            return ['errors' => ['не передан id']];
        }

        return $this->service->delete($this->params['id']);
    }
}