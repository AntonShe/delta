<?php

namespace backend\controllers;

use common\models\language\LanguageService;
use Yii;

class LanguageController extends AbstractController
{
    protected LanguageService $languageService;

    public function __construct($id, $module, $config = [])
    {
        $this->languageService = new LanguageService();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        $this->languageService->setParams($this->params);

        return $this->languageService->getLanguages();
    }
}