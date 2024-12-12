<?php

namespace common\models\banner;

class BannerService
{
    private BannerRepository $repository;

    public function __construct()
    {
        $this->repository = new BannerRepository();
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
        $this->repository->setOrder(['sort' => 'asc']);
        $data = $this->repository->read();

        foreach ($data['banners'] as &$item) {
            $item = BannerDTO::make($item);
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