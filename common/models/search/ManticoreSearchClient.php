<?php

namespace common\models\search;

use common\models\search\index\AbstractIndex;

class ManticoreSearchClient
{

    protected AbstractIndex $index;

    public function __construct(AbstractIndex $index)
    {
        $this->index = $index;
    }

    /**
     * @return void
     */
    public function createIndex(): void
    {
        $this->index->createIndex();
    }

    /**
     * @param array $data
     * @return void
     */
    public function updateDataIndex(array $data): void
    {
        $this->index->addOrUpdateDataToIndex($data);
    }

    /**
     * @return void
     */
    public function runOptimize(): void
    {
        $this->index->runOptimize();
    }

    /**
     * @param array $data
     * @return void
     */
    public function updatePriceAndQuantity(array $data):void
    {
        $this->index->updatePriceAndQuantity($data);
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->index->setParams($params);
    }

    /**
     * @return array
     */
    public function getDataIds(): array
    {
        return $this->index->getIds();
    }
}