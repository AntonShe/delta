<?php

namespace frontend\controllers;

use common\models\order\OrderService;
use common\models\rsb_transaction\RsbTransactionService;
use Yii;
use frontend\controllers\AbstractController;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\web\Response;

class OrderController extends AbstractController
{
    protected OrderService $service;
    protected RsbTransactionService $rsbTransactionService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->rsbTransactionService = new RsbTransactionService();
        $this->service = new OrderService();
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        //Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            /*'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        //'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            /*'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],*/
        ];
    }

    public function actionIndex(): string
    {
        $this->layout = 'vue';
        return $this->renderContent('');
    }

    public function actionCreate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (empty($this->data['orderParams'])) return ['link' => ''];

        return ['id' => $this->service->createOrderFromCart($this->data)];
    }

    public function actionGetSpecifications(): string
    {
        return $this->service->getSpecifications($this->params);
    }

    public function actionGetOrderBill(): string
    {
        return $this->service->getOderBill($this->params);
    }

    public function actionReject(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->service->setParams($this->data);

        return [
            'status' => $this->service->rejectOrder()
        ];
    }


    /**
     * @return array
     */
    public function actionPayment(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['url' => $this->rsbTransactionService->payment($_GET['trans_id'], $_GET['orderNumber'])];
    }


    /**
     * @return Response
     * @throws \Exception
     */
    public function actionResult(): Response
    {
        $transaction = $this->rsbTransactionService->getOrderNumByTransId($_GET['trans_id']);
        if ($transaction) {
            $this->rsbTransactionService->updateStatusPayment($transaction);
        }
        return $transaction
            ? $this->redirect("/profile/orders/{$transaction[0]['order_number']}", 301)
            : $this->redirect(Yii::$app->urlManager->createUrl('site/error'));
    }
}
