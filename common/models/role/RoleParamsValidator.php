<?php

namespace common\models\role;

use common\models\AbstractParamsValidator;

class RoleParamsValidator extends AbstractParamsValidator
{
    public ?string $id = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['id', 'string'],
        ]);
    }
}