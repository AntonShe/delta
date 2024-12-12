<?php

namespace backend\controllers;

use common\models\age\AgeService;
use Yii;

class AgeController extends AbstractController
{
    protected AgeService $ageService;

    public function __construct($id, $module, $config = [])
    {
        $this->ageService = new AgeService();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        $this->ageService->setParams($this->params);

        return $this->ageService->getAges();
    }
}