<?php

namespace frontend\controllers;

use common\models\set\SetService;
use yii\web\Response;

class SetController extends AbstractController
{
    protected SetService $setService;
    public function __construct($id, $module, $config = [])
    {
        $this->setService = new SetService();
        parent::__construct($id, $module, $config);
    }
    public function actionIndex(int $id = null): Response|string
    {
        if (!$id) {
            return $this->redirect('/');
        }

        $this->setService->setParams(['id' => $id]);
        $data = $this->setService->getBigCard();

        if (empty($data)) {
            return $this->redirect('/');
        }

        return $this->render("set-page", [
            'setName' => $data['set']['name'],
            'list' => $data['list']['products'],
            'pagination' => $data['list']['pagination']
        ]);
    }

    public function actionAjax(int $id = null): Response|array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$id) {
            return $this->redirect('/');
        }
        $this->setService->setParams(['id' => $id]);
        $data = $this->setService->getBigCard();

        if (empty($data)) {
            return $this->redirect('/');
        }

        return  [
            'html' => $this->getHtml('@app/views/product_cards/default', $data['list']['products']),
            'pagination' => $data['list']['pagination'],
        ];
    }
}