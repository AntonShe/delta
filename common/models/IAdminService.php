<?php

namespace common\models;

interface IAdminService
{
    /**
     * Get single field by its primary key using entity's repository.
     * @param int $primaryKey
     * @return array
     */
    public function getSingleField(int $primaryKey): array;
    /**
     * Create new field in related entity using entity's repository.
     * @param array $attributes
     * @return array
     */
    public function createSingleField(array $attributes): array;

    /**
     * Update existing field of related entity using entity's repository.
     * @param array $attributes
     * @return array
     */
    public function updateSingleField(array $attributes): array;

    /**
     * Mark existing field of related entity as deleted by its primary key using entity's repository.
     * @param int $primaryKey
     * @return array
     */
    public function deleteSingleField(int $primaryKey): array;
}