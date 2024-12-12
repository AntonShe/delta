<?php

namespace common\models\order;

use common\models\AbstractParamsValidator;

class OrderParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;
    public ?string $token = null;
    public ?string $search = null;

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [['id'], 'integer'],
                [['token', 'search'], 'string']
            ]
        );
    }

    public function getIgnoringValues(): array
    {
        return array_merge(
            parent::getIgnoringValues(),
            []
        );
    }
}