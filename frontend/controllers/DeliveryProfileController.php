<?php

namespace frontend\controllers;

use common\models\delivery_profile\DeliveryProfileService;
use Yii;
use frontend\controllers\AbstractController;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\web\Response;

class DeliveryProfileController extends AbstractController
{
    protected DeliveryProfileService $service;

    public function __construct($id, $module, $config = [])
    {
        $this->service = new DeliveryProfileService();

        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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

    public function actionIndex()
    {
        return $this->service->getLastProfile();
    }

    public function actionGetCityList()
    {
       return $this->service->getCityList();
    }

    public function actionCreate()
    {
        $this->service->setParams($this->data);

        return ['status' => $this->service->create()];
    }
}
