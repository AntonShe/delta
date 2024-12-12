<?php

namespace frontend\controllers;

use common\models\product\ProductService;
use common\models\search\index\ProductIndex;
use common\models\search\ManticoreSearchService;
use yii\web\Response;

class SearchController extends AbstractController
{
    protected ManticoreSearchService $searchService;

    protected ProductService $service;

    public function __construct($id, $module, $config = [])
    {
        $this->searchService = new ManticoreSearchService();
        $this->service = new ProductService();

        parent::__construct($id, $module, $config);
    }

    /**
     * dropdown поиска
     *
     * @return array
     */
    public function actionSearchLine(): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $this->searchService->setClient('client', new ProductIndex());

        $this->params['search'] = $this->service->getPreparedData($this->params['search']);
        $this->searchService->setParams($this->params);

        return $this->searchService->getProductForSearchLine();
    }

    /**
     * Страница поиска
     */
    public function actionSearch(string $search = null): Response|string
    {
        if (!$search) {
            return $this->redirect('/');
        }

        return $this->render("search-page", $this->getDataForSearch($search));
    }

    /**
     * @param string|null $search
     * @return array
     */
    public function actionSearchAjax(string $search = null): array
    {
        $this->response->format = Response::FORMAT_JSON;
        if (!$search || $this->request->isAjax) {
            return ['errors' => 'Что-то пошло не так'];
        }

        $data = $this->getDataForSearch($search, false);

        return [
            'html' => $this->getHtml('@app/views/product_cards/default', $data['list']),
            'pagination' => $data['pagination'],
        ];
    }


    /**
     * @param string $search
     * @param bool $needPopular
     * @return array
     */
    private function getDataForSearch(string $search, bool $needPopular = true): array
    {
        $this->searchService->setClient('client', new ProductIndex());
        $this->searchService->setParams(['search' => $this->service->getPreparedData($search), 'active' => 1, 'is_new' => 0]);

        $ids = $this->searchService->getIdsForSearch();

        $this->addParams(['ids' => $ids]);
        $this->service->setParams($this->params);

        if ($ids) {
            $this->service->setOrder(['FIELD' => $ids]);
        }

        if (isset($this->params['sort']) && $this->params['sort']) {
            $this->service->setOrder([$this->params['sort'] => $this->params['order'] ?? 'asc']);
        }
        $data = $this->service->getProductsForCatalog();

        $popularProducts = [];
        if ($needPopular) {
            $this->addParams(['isPopular' => 1, 'withPagination' => false, 'ids' => $ids]);
            $this->service->setParams($this->params);
            $popularProducts = $this->service->getProductsForCatalog()['products'];
        }

        return [
            'pagination' => $data['pagination'],
            'list' => $data['products'],
            'popularList' => $popularProducts,
            'params' => $this->params,
        ];
    }
}