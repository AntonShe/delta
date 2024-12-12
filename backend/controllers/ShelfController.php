<?php

namespace backend\controllers;

use common\models\shelf\ShelfService;

class ShelfController extends AbstractController implements IAdminController
{
    private ShelfService $service;

    public function __construct($id, $module, $config = [])
    {
        $this->service = new ShelfService();
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     */
    public function actionIndex(): array
    {
        $this->service->setParams($this->params);
        return $this->service->read();
    }

    /**
     * @inheritDoc
     */
    public function actionCreate(): array
    {
        $this->service->setParams($this->data);
        return $this->service->create();
    }

    /**
     * @inheritDoc
     */
    public function actionUpdate(): array
    {
        if (!isset($this->params['id'])) {
            return ['errors' => ['не передан id']];
        }

        $this->service->setParams($this->data);
        return $this->service->update($this->params['id']);
    }

    /**
     * @inheritDoc
     */
    public function actionDelete(): array
    {
        if (!isset($this->params['id'])) {
            return ['errors' => ['не передан id']];
        }

        return $this->service->delete($this->params['id']);
    }
}