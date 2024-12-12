<?php

namespace frontend\widgets\shelves;

use common\models\banner\BannerService;
use common\models\product\ProductService;
use common\models\shelf\ShelfService;
use common\models\trading_shelf_products\TradingShelfProductsService;
use yii\base\Widget;

class ShelvesWidget extends Widget
{
    private ShelfService $service;
    private ProductService $productService;
    private array $list = [];

    public function init()
    {
        $this->service = new ShelfService();
        $this->service->setParams([
            'isActive' => 1,
            'withPagination' => false,
        ]);

        $this->productService = new ProductService();
        $this->productService->setParams(['withPagination' => false]);

        foreach ($this->service->readForCatalog()['shelves'] as $item) {
            $this->productService->setParams(['id' => $item->products]);

            array_multisort($this->productService->getProductsForCatalog()['products'], array_keys($item->products));
            $books = $this->sortProducts(
                $this->productService->getProductsForCatalog()['products'],
                $item->products);
            shuffle($books);
            $this->list[] = [
                'id' => $item->id,
                'name' => $item->name,
                'urlName' => $item->urlName,
                'products' => $books,
            ];
        }

        parent::init();
    }

    public function run(): string
    {
        return $this->render('index', [
            'list' => $this->list,
        ]);
    }

    private function sortProducts(array $reqColumns, array $sortColumns): array
    {
        usort($reqColumns, function($a, $b) use ($sortColumns) {
            $keyCurrent = array_search($a->id,$sortColumns);
            $keyNext = array_search($b->id,$sortColumns);
            if($keyCurrent==$keyNext) return 0;
            return ($keyCurrent>$keyNext) ? 1 : -1;
        });

        return $reqColumns;
    }
}