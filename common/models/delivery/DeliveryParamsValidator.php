<?php

namespace common\models\delivery;

use common\models\AbstractParamsValidator;

class DeliveryParamsValidator extends AbstractParamsValidator
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