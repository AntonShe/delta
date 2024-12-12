<?php

namespace common\models\delivery_profile;

use common\models\AbstractParamsValidator;

class DeliveryProfileParamsValidator extends AbstractParamsValidator
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