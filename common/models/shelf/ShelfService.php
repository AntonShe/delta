<?php

namespace common\models\shelf;


class ShelfService
{
    private ShelfRepository $repository;

    public function __construct()
    {
        $this->repository = new ShelfRepository();
    }

    public function setParams(array $params): void
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
        $this->repository->setOrder(['sort' => 'asc']);
        $data = $this->repository->read(isActive: true);

        foreach ($data['shelves'] as &$item) {
            $item = ShelfDTO::make($item);
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