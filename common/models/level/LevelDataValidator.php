<?php

namespace common\models\level;

use common\models\AbstractDataValidator;

class LevelDataValidator extends AbstractDataValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}