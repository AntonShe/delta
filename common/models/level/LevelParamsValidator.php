<?php

namespace common\models\level;

use common\models\AbstractParamsValidator;

class LevelParamsValidator extends AbstractParamsValidator
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