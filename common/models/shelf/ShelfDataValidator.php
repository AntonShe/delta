<?php

namespace common\models\shelf;

use common\models\AbstractDataValidator;

class ShelfDataValidator extends AbstractDataValidator
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $link = null;
    public ?int $sort = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?array $products = null;
    public ?bool $isActive = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['name'], 'trim'],
            [['name'], 'string'],
            ['sort', 'integer'],
            ['isActive', 'boolean'],
            ['products', 'validateIntArray'],
            [['startDate', 'endDate'], 'date', 'format' => 'php:Y-m-d'],
            ['id', 'safe']
        ]);
    }
}