<?php

namespace common\models\user;

use common\models\AbstractParamsValidator;

class UserParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;
    public string $search= '';
    public ?int $orderNumber = null;
    public string $dateCreateRange = '';

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['id', 'orderNumber'], 'integer', 'min' => 1],
            [['search', 'dateCreateRange'], 'string'],
        ]);
    }
}