<?php

namespace backend\controllers;

use common\models\order\OrderService;
use console\models\OrderSender;

class OrderController extends AbstractController
{
    protected OrderService $service;
    protected PointService $pointService;

    public function __construct($id, $module, $config = [])
    {
        $this->service = new OrderService();
        //$this->pointService = new PointService();

        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return parent::behaviors();

        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionIndex()
    {
        $this->service->setParams($this->params);

        return $this->service->getPreparedOrders();
    }

    public function actionCreate()
    {
        $this->service->setParams($this->data);

        return ['status' => $this->service->createOrder()];
    }

    public function actionUpdate()
    {
        $this->service->setParams($this->data);

        return ['status' => $this->service->updateOrder()];
    }

    public function actionDelete()
    {
        $this->service->setParams($this->params);

        return [
            'status' => $this->service->rejectOrderFromAdmin()
        ];
    }

    public function actionSend(): array
    {
        if (isset($this->params['id'])) {
            return [
                'status' => (new OrderSender())->sendOrder($this->params['id'])
            ];
        }

        return [
            'status' => false
        ];
    }

    public function actionGetOrderFull()
    {
        $this->service->setParams($this->params);

        return $this->service->getOrderFull();
    }
}