<?php

namespace common\models\search;

use common\models\AbstractDataValidator;

class SearchDataValidator extends AbstractDataValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}