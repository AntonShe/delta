<?php

namespace backend\controllers;

use common\models\role\RoleService;
use yii\filters\AccessControl;

class RoleController extends AbstractController
{
    protected RoleService $roleService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->roleService = new RoleService();
    }

    public function behaviors(): array
    {
        /*
        User
        */
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    echo '<a href="'.Yii::$app->getHomeUrl().'"><button>Назад</button></a>';
                    echo '<h3>Доступ ограничен!</h3>';
                },
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['admin']
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->roleService->getRoles($this->data['id'] ?? 0);
    }
}