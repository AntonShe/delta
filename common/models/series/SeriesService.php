<?php

namespace common\models\series;

use common\models\product\ProductService;
use common\models\publishing_house\PublishingHouseService;

class SeriesService
{
    protected SeriesRepository $seriesRepository;
    protected PublishingHouseService $publishingHouseService;

    protected ProductService $productService;

    public function __construct()
    {
        $this->seriesRepository = new SeriesRepository();
        $this->productService = new ProductService();
        $this->publishingHouseService = new PublishingHouseService();
    }

    public function setParams(array $params)
    {
        $this->seriesRepository->setParams($params);
    }

    public function getBigCard(): array|null
    {
        if ($series = $this->seriesRepository->getSeries()[0]) {

            $this->publishingHouseService->setParams(['id' => $series['publishingHouseId'], 'withPagination' => false]);
            $publisher = $this->publishingHouseService->getBigCard();

            $this->productService->setParams(['seriesId' => $series['id']]);
            $products = $this->productService->getProductsForCatalog();

            if (empty($publisher)) {
                $this->publishingHouseService->setParams(['id' => $products['products'][0]->publishingHouseId, 'withPagination' => false]);
                $publisher = $this->publishingHouseService->getBigCard();
            }

            return [
                'series' => $series,
                'listProducts' => $products,
                'publisher'=> $publisher
                ];
        }
        return null;
    }
}