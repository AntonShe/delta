<?php

namespace frontend\controllers;

use common\models\api\lpostApi\LpostClient;
use common\models\api\yandexMaps\YandexClient;
use common\models\order\OrderService;
use common\models\points\PointService;
use common\models\product\ProductEntity;
use common\models\product\ProductService;
use common\models\rating\RatingService;
use common\models\user\UserService;
use console\models\OrderSender;
use frontend\controllers\AbstractController;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\login\LoginFormService;
use yii\web\Controller;
use yii\web\JsonResponseFormatter;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends AbstractController
{
    public function beforeAction($action): bool
    {
        $pathInfo = Yii::$app->getRequest()->getPathInfo();
        $isFavorite = strpos($pathInfo, 'favourite');

        if (Yii::$app->user->isGuest  && $action->id === 'profile' && !$isFavorite) {

            $this->redirect(['site/index']);
            return false;
        }

        return parent::beforeAction($action);
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            if ($exception->statusCode == 404)
                return $this->render('error404', ['exception' => $exception]);
            else
                return $this->render('error', ['exception' => $exception]);
        }
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'isGuest' => $this->isLocal
        ]);
    }

    public function actionUserAgreement()
    {
        return $this->render('userAgreement');
    }

    public function actionPublicOfferPerson()
    {
        return $this->render('publicOfferPerson');
    }

    public function actionPublicOffer()
    {
        return $this->render('publicOffer');
    }

    public function actionPaymentRefund()
    {
        return $this->render('paymentRefund');
    }

    public function actionContacts()
    {
        return $this->render('contactsPage');
    }

    public function actionGetForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = \Yii::$app->request->post();

        if (empty($data) && $data !== 0) return '';

        return [
            'content' => LoginFormService::renderForm($data)
        ];
    }

    /** Displays Delivery Page.*/
    public function actionDelivery()
    {
        $this->layout = 'vue';
        return $this->renderContent('');
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionCart(): string
    {
        $this->layout = 'vue';
        return $this->renderContent('');
    }

    public function actionProfile(): string
    {
        $this->layout = 'vue';
        return $this->renderContent('');
    }
}
