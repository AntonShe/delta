<?php

namespace common\models\favorite;

use common\models\AbstractDataValidator;

class FavoriteDataValidator extends AbstractDataValidator
{
    public ?int $id = null;
    public ?array $ids = null;
    public ?int $userId = null;
    public ?int $productId = null;
    public ?string $sessionKey = null;

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [['id', 'userId', 'productId',], 'integer'],
                [['sessionKey'], 'string'],
                ['ids', 'validateIntArray'],
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