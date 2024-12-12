<?php

namespace common\models\rating;

use common\models\AbstractParamsValidator;

class RatingParamsValidator extends AbstractParamsValidator
{
    public ?int $productId = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['productId', 'integer', 'min' => 1],
            ['productId', 'required'],
        ]);
    }
}