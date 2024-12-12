<?php

namespace common\models\favorite;

use common\models\product\ProductService;
use common\models\user\UserTrait;

class FavoriteService
{
    use UserTrait;

    protected FavoriteRepository $repository;
    protected ProductService $productService;

    public function __construct()
    {
        $this->repository = new FavoriteRepository();
        $this->productService = new ProductService();
    }

    public function setParams(array $data): void
    {
        $this->repository->setParams($data);
    }

    public function getFavorite(): array
    {
        $this->setUserParams();

        return $this->repository->getFavorite();
    }

    public function getFavoriteFull(): array
    {
        $this->setUserParams();

        return $this->repository->getFavoriteFull();
    }

    public function getFavoriteProductFull(): array
    {
        $this->setUserParams();
        $favoriteList =  $this->repository->getFavorite();

        if (empty($favoriteList)) return ['products' => []];

        $this->productService->setParams([
            'ids' => $favoriteList
        ]);

        return $this->productService->getProducts();
    }

    public function addFavorite(): bool
    {
        $this->setUserParams();
        return $this->repository->addFavorite();
    }

    public function deleteFavorite(): bool
    {
        $this->setUserParams();
        return $this->repository->deleteFavorite();
    }

    protected function setUserParams(): void
    {
        $params = $this->getUserParams();

        $this->repository->mergeParams($params);
    }

    /**
     * @return bool
     */
    public function updateFavourite(): bool
    {
        return $this->repository->update();
    }
}