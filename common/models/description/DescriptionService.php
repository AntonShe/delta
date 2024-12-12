<?php

namespace common\models\description;

use common\models\course\CourseService;
use common\models\genre\GenreService;
use JetBrains\PhpStorm\ArrayShape;

class DescriptionService implements \common\models\IAdminService
{
    private DescriptionRepository $repository;

    public function __construct()
    {
        $this->repository = new DescriptionRepository();
    }

    /**
     * @inheritDoc
     */
    public function getSingleField(int $primaryKey): array
    {
        return $this->repository->read($primaryKey);
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'pagination' => "array",
        'descriptions' => "\array|\yii\db\ActiveRecord[]"
    ])]
    public function getAllFields(): array
    {
        return $this->repository->readPage();
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'success' => "bool",
        'description' => "array",
        'errors' => "array"
    ])]
    public function createSingleField(array $attributes): array
    {
        return $this->repository->create($attributes);
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'success' => "bool",
        'description' => "array",
        'errors' => "array"
    ])]
    public function updateSingleField(array $attributes): array
    {
        return $this->repository->update($attributes);
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'success' => "bool",
        'description' => "array",
        'errors' => "array"
    ])]
    public function deleteSingleField(int $primaryKey): array
    {
        return $this->repository->delete($primaryKey);
    }
}