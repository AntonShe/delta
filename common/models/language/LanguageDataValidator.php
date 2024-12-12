<?php

namespace common\models\language;

use common\models\AbstractDataValidator;

class LanguageDataValidator extends AbstractDataValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}