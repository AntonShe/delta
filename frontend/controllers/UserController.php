<?php

namespace frontend\controllers;

use common\models\user\UserService;
use Yii;
use frontend\controllers\AbstractController;

use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\web\Response;

class UserController extends AbstractController
{
    protected UserService $userService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->userService = new UserService();
        $this->response->format = Response::FORMAT_JSON;
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

    public function actionSendPin()
    {
        $this->userService->setParams($this->data);

        return [
            'result' => $this->userService->createPin(),
            'keyHash' => $this->userService->getLastPinHash()
        ];
    }

    public function actionVerifyPin()
    {
        $this->userService->setParams($this->data);

        return [
            'result' => $this->userService->verifyPin()
        ];
    }

    public function actionSaveUser()
    {
        if (isset($this->data['isNew']) && $this->data['isNew'] === 0) {
            $result = $this->userService->updatePassword($this->data);
        } else {
            $result = $this->userService->createUser($this->data, true);
        }

        return [
            'result' => $result
        ];
    }

    public function actionAuthUser()
    {
        $type = (!empty($this->data['email']) && !empty($this->data['password'])) ? 1 : 2;
        $this->userService->setParams($this->data);
        $user = $this->userService->getUsers();
        $data['result'] = $this->userService->authUser($this->data, $type);

        if ($data['result']) {
            $data['isNewUser'] = empty($user['users']);
        }

        return $data;
    }

    public function actionGetUserBadge()
    {
        return [
            'badge' => $this->userService->getBadge(),
            'isGuest' => \Yii::$app->user->isGuest,
            'city' => $this->userService->getUserCity()
        ];
    }

    public function actionGetUserInfo()
    {
        return $this->userService->getFullUserInfo();
    }

    public function actionUpdateUser()
    {
        $status = $this->userService->updateUser($this->data);

        if ($status === true) {
            return [
                'status' => $status
            ];
        } else {
            return [
                'status' => false,
                'errors' => $status
            ];
        }
    }

    public function actionGetOrders()
    {
        return $this->userService->getOrders($this->params);
    }

    public function actionGetUserCity(): array
    {
        return [
            'isGuest' => \Yii::$app->user->isGuest,
            'city' => $this->userService->getUserCity()
        ];
    }

    public function actionSetUserCity(): array
    {
        if (!isset($this->data['city']) && !$this->data['city']) {
            return [ 'status' => false ];
        }
        
        return [
            'status' => $this->userService->updateUserCity($this->data)
        ];
    }
}
