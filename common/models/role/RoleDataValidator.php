<?php

namespace common\models\role;

use common\models\AbstractDataValidator;

class RoleDataValidator extends AbstractDataValidator
{
    public ?string $id = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['id'], 'string'],
        ]);
    }
}