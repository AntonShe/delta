<?php

namespace common\models\site;

use \common\models\AbstractDataValidator;

class SiteDataValidator extends AbstractDataValidator
{
    public ?int $value = null;
    public ?int $productId = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['value', 'productId'], 'integer', 'min' => 1],
        ]);
    }
}