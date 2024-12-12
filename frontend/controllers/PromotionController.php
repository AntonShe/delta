<?php

namespace frontend\controllers;

use common\models\promotion\PromotionService;
use yii\web\Response;

class PromotionController extends AbstractController
{
    protected PromotionService $service;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->service = new PromotionService();
    }

    /**
     * Получение страницы акций
     */
    public function actionIndex(): Response|string
    {
        return $this->render("index", ['list' => $this->service->readForCatalog()['data']]);
    }
}
