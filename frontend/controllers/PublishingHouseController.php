<?php

namespace frontend\controllers;

use common\models\product\ProductService;
use common\models\publishing_house\PublishingHouseService;
use yii\web\Response;

class PublishingHouseController extends AbstractController
{
    protected PublishingHouseService $publishingHouseService;
    protected ProductService $productService;
    public function __construct($id, $module, $config = [])
    {
        $this->publishingHouseService = new PublishingHouseService();
        $this->productService = new ProductService();

        parent::__construct($id, $module, $config);
    }

    /**
     * @param int|null $id
     * @return Response|string
     */
    public function actionIndex(int $id = null): Response|string
    {
        if (!$id) {
            return $this->redirect('/');
        }

        list($publisher, $data) = $this->getData($id);

        if (empty($publisher)) {
            return $this->redirect('/');
        }

        return $this->render("@app/views/publishing_house/publisher-page", [
            'publisher' => $publisher,
            'list' => $data['products'],
            'pagination' => $data['pagination']
        ]);
    }

    /**
     * @param int|null $id
     * @return Response|array
     */
    public function actionAjax(int $id = null): Response|array
    {
        $this->response->format = Response::FORMAT_JSON;

        if (!$id) {
            return $this->redirect('/');
        }

        list($publisher, $data) = $this->getData($id);

        if (empty($publisher)) {
            return $this->redirect('/');
        }

        return [
            'html' => $this->getHtml('@app/views/product_cards/default', $data['products']),
            'pagination' => $data['pagination']
        ];
    }

    /**
     * @param int $id
     * @return Response|array
     */
    private function getData(int $id): Response|array
    {
        $this->publishingHouseService->setParams(['id' => $id, 'withPagination' => false]);
        $publisher = $this->publishingHouseService->getBigCard();

        $this->productService->setParams(['publishingHouseId' => $id, 'active' => 1, 'is_new' => 0]);
        $data = $this->productService->getProductsForCatalog();

        return [$publisher, $data];
    }
}