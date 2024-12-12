<?php

namespace common\models\catalog;

use common\models\AbstractParamsValidator;

class CatalogParamsValidator extends AbstractParamsValidator
{
    public ?string $search = null;
    public ?array $words = null;
    public ?array $ages = null;
    public ?array $genres = null;
    public ?array $publishingHouseId = null;
    public ?array $ageCategoryId = null;
    public ?array $parentId = null;
    public ?array $levels = null;
    public ?string $sort = null;
    public ?string $order = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['genres', 'ages', 'ageCategoryId', 'parentId', 'publishingHouseId', 'levels'], 'validateIntArray'],
            ['words', 'validateStringArray'],
            [['search', 'sort', 'order'], 'string'],
        ]);
    }
}