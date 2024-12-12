<?php

namespace backend\controllers;

use common\models\promotion\PromotionService;
use JetBrains\PhpStorm\ArrayShape;

class PromotionController extends AbstractController implements IAdminController
{
    private PromotionService $service;

    public function __construct($id, $module, $config = [])
    {
        $this->service = new PromotionService();

        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'pagination' => "array",
        'promotions' => "\array|\yii\db\ActiveRecord[]"
    ])]
    public function actionIndex(): array
    {
        $this->service->setParams($this->params);
        return $this->service->read();
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'success' => "bool",
        'data' => "array",
        'errors' => "array"
    ])]
    public function actionCreate(): array
    {
        $this->service->setParams($this->data);
        return $this->service->create();
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'success' => "bool",
        'data' => "array",
        'errors' => "array"
    ])]
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
    #[ArrayShape([
        'success' => "bool",
        'data' => "array",
        'errors' => "array"
    ])]
    public function actionDelete(): array
    {
        if (!isset($this->params['id'])) {
            return ['errors' => ['не передан id']];
        }

        return $this->service->delete($this->params['id']);
    }
}