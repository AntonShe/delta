<?php

namespace common\models\set;

use common\models\AbstractDataValidator;

class SetDataValidator extends AbstractDataValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}