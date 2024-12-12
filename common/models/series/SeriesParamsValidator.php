<?php

namespace common\models\series;

use common\models\AbstractParamsValidator;

class SeriesParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;
    public ?string $name = null;
    public ?int $publishingHouseId = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['id','publishingHouseId'], 'integer', 'min' => 1],
            ['name', 'string'],
        ]);
    }
}