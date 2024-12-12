<?php

namespace common\models\cart;

use common\models\AbstractParamsValidator;

class CartParamsValidator extends AbstractParamsValidator
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            []
        );
    }

    public function getIgnoringValues(): array
    {
        return array_merge(
            parent::getIgnoringValues(),
            []
        );
    }
}