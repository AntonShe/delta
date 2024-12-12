<?php

namespace frontend\widgets\courses_on_main;

use common\models\genre\GenreService;
use common\models\product\ProductService;
use yii\base\Widget;

class CoursesOnMainWidget extends Widget
{
    public function run(): string
    {
        $service = new GenreService();
        $service->setParams(['onMain' => 1, 'isCourse' => 1, 'limit' => 1]);
        $data = $service->getGenresByCatalog()[0] ?? [];

        if (empty($data) || empty($data->onMainInfo['products'])) {
            return '';
        }
        $productService = new ProductService();
        $productService->setParams(['withPagination' => false, 'id' => $data->onMainInfo['products'], 'active' => 1]);
        $products = $productService->getProductsForCatalog()['products'];

        return $this->render('index', [
            'data' => $data,
            'products' => $products
        ]);
    }
}