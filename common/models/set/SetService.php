<?php

namespace common\models\set;

use common\models\product\ProductService;

class SetService
{
    protected SetRepository $repository;
    protected ProductService $productService;

    public function __construct()
    {
        $this->repository = new SetRepository();
        $this->productService = new ProductService();
    }

    /**
     * @param array $params
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->repository->setParams($params);
    }

    /**
     * @return array|null
     */
    public function getBigCard(): ?array
    {
        $set = $this->repository->getSets();

        if ($set['sets']) {

            $this->productService->setParams([
                'inStock' => true,
                'genres' => $this->repository->getGenres(),
                'isNew' => 0
            ]);

            return [
                'list' => $this->productService->getProductsForCatalog(),
                'set' => $set['sets'][0]
            ];
        }

        return null;
    }
}