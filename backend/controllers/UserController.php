<?php

namespace backend\controllers;

use common\models\user\UserService;
use Yii;
use backend\controllers\AbstractController;

use common\models\LoginForm;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\web\Response;

/**
 * Site controller
 */
class UserController extends AbstractController
{
    protected UserService $userService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->userService = new UserService();
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
        try {
            $this->userService->setParams($this->params);

            return [
                'data' => $this->userService->getUsers(),
                'message' => '',
                'code' =>  0
            ];
        } catch (\yii\base\Exception $e) {
            return [
                'status' => 0,
                'message' => $e->getMessage(),
                'code' =>  2
            ];
        }
    }

    public function actionAuth()
    {
        try {
            $isAuthorized = $this->userService->authUser(
                $this->data,
                $this->userService::AUTHORIZATION_TYPES['adminEmailPass']
            );

            return [
                'status' => $isAuthorized,
                'message' => '',
                'code' =>  0
            ];

        } catch (\yii\base\Exception $e) {
            return [
                'status' => 0,
                'message' => $e->getMessage(),
                'code' =>  2
            ];
        }
    }

    public function actionCreate()
    {
        return [
            'status' => (int)$this->userService->createUser($this->data),
        ];
    }

    public function actionUpdate()
    {
        return [
            'status' => (int)$this->userService->updateUser($this->data),
        ];
    }
}
