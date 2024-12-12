<?php

namespace backend\controllers;

use console\models\OrderSender;
use Yii;
use yii\filters\AccessControl;
use yii\rest\Controller;

/**
 * Site controller
 */
class AdminController extends Controller
{
    public bool $isLocal;

    public function beforeAction($action)
    {
        if (!Yii::$app->user->isGuest && !Yii::$app->user->isAdmin())  {
            $this->redirect(Yii::$app->urlManagerFrontEnd->createUrl('site/error'));
            return false;
        }

        if (Yii::$app->user->isGuest && $action->id !== 'login') {
            $this->redirect(['admin/login']);
            return  false;
        }

        if ($action->id == 'logout') {
            if (!\Yii::$app->user->isGuest) \Yii::$app->user->logout();

            $this->redirect(['admin/login']);

            return  false;
        }

        $host = explode(":", $_SERVER['HTTP_HOST'])[0];
        $this->isLocal = \Yii::$app->params['localserver'] && $host == 'localhost';

        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
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
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@']
                    ],
                    [//Роль маркетинг
                        'allow' => true,
                        'actions' => [
                            'marketing-banners',
                            'marketing-shelves',
                            'marketing-promotions',
                            'orders'
                        ],
                        'roles' => ['marketing', 'admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'marketing-genres',
                        ],
                        'roles' => ['biblio', 'marketing', 'admin'],
                    ],
                    [//Роль продажники
                        'allow' => true,
                        'actions' => ['orders', 'products', 'user'],
                        'roles' => ['seller', 'admin'],
                    ],
                    [//Роль библиография
                        'allow' => true,
                        'actions' => ['products', 'products-new', 'persons', 'publishers'],
                        'roles' => ['biblio', 'admin'],
                    ],
                    [//Роль админ
                        'allow' => true,
                        'actions' => ['user', 'update-orders'],
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    public function getIsLocal(): bool
    {
        return $this->isLocal;
    }

    public function actionIndex(): string
    {
        return $this->renderContent('');
    }

    public function actionLogin(): string
    {
        return $this->renderContent('');
    }

    public function actionProducts(): string
    {
        return $this->renderContent('');
    }

    public function actionProductsNew(): string
    {
        return $this->renderContent('');
    }

    public function actionPersons(): string
    {
        return $this->renderContent('');
    }

    public function actionOrders(): string
    {
        return $this->renderContent('');
    }

    public function actionPublishers(): string
    {
        return $this->renderContent('');
    }

    public function actionMarketingGenres(): string
    {
        return $this->renderContent('');
    }

    public function actionMarketingBanners(): string
    {
        return $this->renderContent('');
    }

    public function actionMarketingShelves(): string
    {
        return $this->renderContent('');
    }

    public function actionMarketingPromotions(): string
    {
        return $this->renderContent('');
    }

    public function actionUser(): string
    {
        return $this->renderContent('');
    }

    public function actionLogout(): string
    {
        return '';
    }

    public function actionUpdateOrders(): string
    {
        $sender = new OrderSender();
        $sender->sendNewOrders();
        var_dump('Done');
        return  '';
    }

    public function actionError($error): string
    {
        var_dump("Все сломалось! Сделайте скриншот и обратитесь в отдел технологий");
        var_dump($error);
        return $this->renderContent('');
    }
}
