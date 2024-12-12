<?php

namespace backend\controllers;

use common\models\level\LevelService;
use Yii;

class LevelController extends AbstractController
{
    protected LevelService $levelService;

    public function __construct($id, $module, $config = [])
    {
        $this->levelService = new LevelService();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        $this->levelService->setParams($this->params);

        return $this->levelService->getLevels();
    }
}