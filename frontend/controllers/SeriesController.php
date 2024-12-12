<?php

namespace frontend\controllers;

use common\models\product\ProductService;
use common\models\publishing_house\PublishingHouseService;
use common\models\series\SeriesService;
use yii\web\Response;

class SeriesController extends AbstractController
{
    protected SeriesService $seriesService;
    protected ProductService $productService;
    protected PublishingHouseService $publishingHouseService;
    public function __construct($id, $module, $config = [])
    {
        $this->seriesService = new SeriesService();
        $this->productService = new ProductService();
        $this->publishingHouseService = new PublishingHouseService();

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

        $this->seriesService->setParams(['id' => $id]);
        $data = $this->seriesService->getBigCard();

        if (empty($data)) {
            return $this->redirect('/');
        }

        return $this->render("series-page", [
            'series' => $data['series'],
            'publisher' => $data['publisher'],
            'list' => $data['listProducts']['products'],
            'pagination' => $data['listProducts']['pagination']
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

        $this->seriesService->setParams(['id' => $id]);
        $data = $this->seriesService->getBigCard();

        if (empty($data)) {
            return $this->redirect('/');
        }

        return  [
            'html' => $this->getHtml('@app/views/product_cards/default', $data['listProducts']['products']),
            'pagination' => $data['listProducts']['pagination'],
        ];
    }
}