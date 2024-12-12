<?php

namespace common\models\promotion;

class PromotionService
{
    private PromotionRepository $repository;

    public function __construct()
    {
        $this->repository = new PromotionRepository();
    }

    public function setParams($params): void
    {
        $this->repository->setParams($params);
    }

    public function read(): array
    {
        $this->repository->setOrder(['id' => 'desc']);

        return $this->repository->read();
    }

    public function readForCatalog(): array
    {
        $this->repository->setOrder(['id' => 'desc']);
        $data = $this->repository->read();

        foreach ($data['data'] as &$item) {
            $item = PromotionDTO::make($item);
        }

        return $data;
    }

    public function create(): array
    {
        return $this->repository->create();
    }

    public function update(int $id): array
    {
        return $this->repository->update($id);
    }

    public function delete(int $id): array
    {
        return $this->repository->delete($id);
    }
}