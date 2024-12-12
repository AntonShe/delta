<?php

namespace common\models\age;

use common\models\AbstractParamsValidator;

class AgeParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;
    public array $ids = [];

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['id', 'integer'],
            ['ids', 'validateIntArray'],
        ]);
    }
}