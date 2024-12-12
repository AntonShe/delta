<?php

namespace frontend\controllers;

use common\models\person\PersonService;
use common\models\product\ProductService;
use yii\web\Response;

class PersonController extends AbstractController
{

    protected PersonService $personService;
    protected ProductService $productService;
    public function __construct($id, $module, $config = [])
    {
        $this->personService = new PersonService();
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

        list($person, $data) = $this->getData($id);

        if (empty($person)) {
            return $this->redirect('/');
        }

        return $this->render("person-page", [
            'person' => $person,
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

        list($person, $data) = $this->getData($id);

        if (empty($person)) {
            return $this->redirect('/');
        }

        return  [
            'html' => $this->getHtml('@app/views/product_cards/default', $data['products']),
            'pagination' => $data['pagination'],
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    private function getData(int $id): array
    {
        $this->personService->setParams(['id' => $id, 'withPagination' => false]);
        $person = $this->personService->getBigCard();

        $this->personService->setParams(['personId' => $id]);
        $productIds = $this->personService->getProductIdsByPerson();

        $this->productService->setParams(['ids' => $productIds]);
        $data = $this->productService->getProductsForCatalog();

        return [$person, $data];
    }
}