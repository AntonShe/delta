<?php

namespace backend\controllers;

use common\models\product\ProductService;
use Yii;

class ProductController extends AbstractController
{
    protected ProductService $productService;

    public function __construct($id, $module, $config = [])
    {
        $this->productService = new ProductService();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        $this->productService->setParams($this->params);
        $this->productService->setOrder(['title' => 'ASC']);

        return $this->productService->getProducts();
    }

    public function actionUpdate(): array
    {
        if (!isset($this->params['id'])) {
            return ['errors' => ['не передан id товара']];
        }

        $this->productService->setParams($this->data);
        return $this->productService->updateProduct($this->params['id']);
    }
}