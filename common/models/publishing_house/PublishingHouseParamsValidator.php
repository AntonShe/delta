<?php

namespace common\models\publishing_house;

use common\models\AbstractParamsValidator;

class PublishingHouseParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;
    public array $ids = [];
    public string $search= '';

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['id', 'integer'],
            ['ids', 'validateIntArray'],
            ['search', 'string'],
        ]);
    }
}