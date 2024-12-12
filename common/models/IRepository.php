<?php

namespace common\models;

interface IRepository
{
    /**
     * Reads cortege by its ID.
     * @param int $fieldID
     * @return array
     */
    public function read(int $fieldID): array;

    /**
     * Creates new cortege in related DB.
     * @param array $attributes
     * @return array
     */
    public function create(array $attributes): array;

    /**
     * Updates selected cortege by its ID.
     * @param array $attributes
     * @return array
     */
    public function update(array $attributes): array;

    /**
     * Marks selected cortege as deleted by its ID.
     * @param int $fieldID
     * @return array
     */
    public function delete(int $fieldID): array;

}