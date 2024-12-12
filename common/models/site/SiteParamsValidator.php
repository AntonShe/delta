<?php

namespace common\models\site;

use \common\models\AbstractParamsValidator;

class SiteParamsValidator extends AbstractParamsValidator
{
    public ?int $price = null;
    public ?int $productId = null;
    public ?bool $isNew = null;
    public ?array $genres = null;
    public ?bool $active = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['price', 'productId'], 'integer', 'min' => 1],
            [['genres'], 'validateIntArray'],
            [['isNew', 'active'], 'boolean'],
        ]);
    }
}