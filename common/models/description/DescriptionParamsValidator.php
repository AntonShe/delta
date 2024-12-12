<?php

namespace common\models\description;

use common\models\AbstractParamsValidator;

class DescriptionParamsValidator extends AbstractParamsValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}