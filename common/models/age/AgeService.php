<?php

namespace common\models\age;

use common\models\age\AgeDTO;

class AgeService
{
    protected AgeRepository $repository;

    public function __construct()
    {
        $this->repository = new AgeRepository();
    }

    public function setParams($params): void
    {
        $this->repository->setParams($params);
    }

    public function getAges(): array
    {
        return $this->repository->getAges();
    }

    public function getAgesByCatalog(): array
    {
        $list = $this->repository->getAges();
        foreach ($list as &$item) {
            $item = AgeDTO::make($item);
        }
        return $list;
    }
}