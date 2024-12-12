<?php

namespace common\models\age;

use common\models\AbstractDataValidator;

class AgeDataValidator extends AbstractDataValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}