<?php

namespace common\models\product;

use common\models\AbstractParamsValidator;

class ProductParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;
    public ?string $simpleSearch = null;
    public ?array $genres = null;
    public ?array $ids = null;
    public ?bool $isNew = null;
    public ?int $price = null;
    public ?array $publishingHouseId = null;
    public ?array $ages = null;
    public ?array $levels = null;
    public ?int $limit = null;
    public ?string $search = null;
    public ?bool $active = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['id', 'price', 'limit'], 'integer', 'min' => 1],
            [['isNew', 'active'], 'boolean'],
            [['ids', 'genres', 'ages', 'levels', 'publishingHouseId'], 'validateIntArray'],
            [['simpleSearch', 'search'], 'string'],
        ]);
    }
}