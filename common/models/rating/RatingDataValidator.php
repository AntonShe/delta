<?php

namespace common\models\rating;

use common\models\AbstractDataValidator;

class RatingDataValidator extends AbstractDataValidator
{
    public ?int $productId = null;
    public ?int $value = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['productId', 'value'], 'integer', 'min' => 1],
            [['productId', 'value'], 'required'],
        ]);
    }
}