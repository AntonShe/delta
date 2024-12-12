<?php

namespace common\models\shelf;

use common\models\AbstractParamsValidator;
use common\models\AbstractValidator;

class ShelfParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['id', 'integer', 'min' => 1]
        ]);
    }
}