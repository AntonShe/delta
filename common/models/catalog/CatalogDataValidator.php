<?php

namespace common\models\catalog;

use common\models\AbstractDataValidator;

class CatalogDataValidator extends AbstractDataValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}