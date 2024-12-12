<?php

namespace common\models\banner;

use common\models\AbstractParamsValidator;

class BannerParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['id', 'integer', 'min' => 1]
        ]);
    }
}