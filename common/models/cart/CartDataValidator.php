<?php

namespace common\models\cart;

use common\models\AbstractDataValidator;

class CartDataValidator extends AbstractDataValidator
{
    public ?int $id = null;
    public ?array $ids = null;
    public ?int $userId = null;
    public ?string $sessionKey = null;

    public ?int $productId = null;
    public ?int $quantity = null;

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [['id', 'userId', 'productId', 'quantity'], 'integer'],
                ['ids', 'validateIntArray'],
                ['sessionKey', 'string']
            ]
        );
    }

    public function getIgnoringValues(): array
    {
        return array_merge(
            parent::getIgnoringValues(),
            [0, '']
        );
    }
}