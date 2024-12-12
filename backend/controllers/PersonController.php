<?php

namespace backend\controllers;

use common\models\person\PersonService;
use Yii;

class PersonController extends AbstractController
{
    protected PersonService $personService;
    public function __construct($id, $module, $config = [])
    {
        $this->personService = new PersonService();

        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return parent::behaviors();

        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionIndex(): array
    {
        $this->personService->setParams($this->params);

        return [
            'data' => $this->personService->getPersons()
        ];
    }

    public function actionCreate()
    {
        return [
            'status' => (int)$this->personService->createPerson($this->data)
        ];
    }

    public function actionUpdate()
    {
        return [
            'status' => (int)$this->personService->updatePerson($this->data)
        ];
    }
}