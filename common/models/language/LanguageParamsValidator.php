<?php

namespace common\models\language;

use common\models\AbstractParamsValidator;

class LanguageParamsValidator extends AbstractParamsValidator
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