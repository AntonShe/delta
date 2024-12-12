<?php

namespace common\models\set;

use common\models\AbstractParamsValidator;

class SetParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['id', 'integer', 'min' => 1]
        ]);
    }
}