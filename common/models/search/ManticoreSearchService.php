<?php

namespace common\models\search;

use common\models\product\ProductService;
use common\models\search\index\AbstractIndex;
use common\models\user\UserService;

class ManticoreSearchService
{
    protected ProductService $productService;
    protected ManticoreSearchPDO|ManticoreSearchClient $client;
    public function __construct()
    {
        $this->productService = new ProductService();
    }

    /**
     * @param string $conn
     * @param AbstractIndex $abstractIndex
     */
    public function setClient(string $conn, AbstractIndex $abstractIndex): void
    {
        $this->client = match ($conn){
            'PDO' => new ManticoreSearchPDO(),
            'client' => new ManticoreSearchClient($abstractIndex)
        };
    }

    /**
     * @return void
     */
    public function addOrUpdateProductForIndex(): void
    {
        $i = 1;
        do {
            $products = $this->productService->getProductsForManticoreSearch($i);

            print_r('Start chunk' . PHP_EOL);
            $this->client->updateDataIndex($products['products']);
            print_r('End chunk' . PHP_EOL);

            $i++;
        } while ($products['pagination']['pageCount'] >= $i);

        $this->client->runOptimize();

    }

    public function createIndex(): void
    {
        $this->client->createIndex();
    }

    /**
     * @param array $data
     * @return void
     */
    public function updatePricesAndQuantity(array $data): void
    {
        $this->client->updatePriceAndQuantity($data);
    }

    /**
     * @return array
     */
    public function getProductForSearchLine(): array
    {
        $searchProducts = $this->client->getDataIds();

        $this->productService->setParams(['ids' => $searchProducts]);
        $this->productService->setOrder([['FIELD' => $searchProducts]]);

        return $this->productService->getProducts();
    }

    /**
     * @param array $params
     * @return void
     */
    public function setParams(array $params): void
    {
        if (isset($params['search'])) {
            $params['search'] = $this->productService->getPreparedData($params['search']);
        }
        $this->client->setParams($params);
    }

    /**
     * @return array
     */
    public function getIdsForSearch(): array
    {
        return $this->client->getDataIds();
    }

    /**
     * @return void
     */
    public function addOrUpdateUserForIndex(): void
    {
        $i = 1;
        do {
            $userService = new UserService();
            $users = $userService->getUsersForManticoreSearch($i);

            print_r('Start chunk' . PHP_EOL);
            $this->client->updateDataIndex($users['users']);
            print_r('End chunk' . PHP_EOL);

            $i++;
        } while ($users['pagination']['pageCount'] >= $i);

        $this->client->runOptimize();

    }
}