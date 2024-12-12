<?php

namespace frontend\controllers;

use common\models\cart\CartService;
use frontend\controllers\AbstractController;
use yii\web\Response;

class CartController extends AbstractController
{
    protected CartService $cartService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->cartService = new CartService();
        $this->response->format = Response::FORMAT_JSON;
    }

    public function actionGetCart()
    {
        return $this->cartService->getCart();
    }

    public function actionGetTotalCount()
    {
        return [
            'total' => $this->cartService->getCartTotalCount()
        ];
    }

    public function actionAddToCart()
    {
        $this->cartService->setParams($this->data);

        return [
            'status' => $this->cartService->addToCart()
        ];
    }

    public function actionSetQuantity()
    {
        $this->cartService->setParams($this->data);

        return [
            'status' => $this->cartService->SetQuantity()
        ];
    }

    public function actionDeleteItems()
    {
        $this->cartService->setParams($this->data);

        return [
            'status' => $this->cartService->deleteItems()
        ];
    }

    public function actionItemCartId()
    {
        return ['id' => $this->cartService->getItemId($this->data)];
    }
}