<?php

namespace common\models\series;

use common\models\AbstractDataValidator;

class SeriesDataValidator extends AbstractDataValidator
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