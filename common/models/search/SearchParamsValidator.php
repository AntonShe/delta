<?php

namespace common\models\search;

use common\models\AbstractParamsValidator;

class SearchParamsValidator extends AbstractParamsValidator
{
    public ?int $limit = null;
    public ?string $search = null;
    public ?string $sort = null;
    public ?string $order = null;
    public ?int $active = null;
    public ?array $levels = null;
    public ?array $publishingHouseId = null;
    public ?array $ages = null;
    public ?array $genres = null;
    public ?int $is_new = null;
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['genres', 'ages', 'publishingHouseId', 'levels'], 'validateIntArray'],
            [['limit'], 'integer', 'min' => 1],
            [['active', 'is_new'], 'integer', 'max' => 1],
            [['search', 'sort', 'order'], 'string']
        ]);
    }
}