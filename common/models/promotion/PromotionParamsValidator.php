<?php

namespace common\models\promotion;

use common\models\AbstractParamsValidator;

class PromotionParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['id', 'integer', 'min' => 1]
        ]);
    }
}