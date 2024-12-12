<?php

namespace common\models\delivery_profile;

use common\models\AbstractDataValidator;

class DeliveryProfileDataValidator extends AbstractDataValidator
{
    public ?int $id = null;
    public ?array $ids = null;
    public ?int $userId = null;

    public string $address = '';
    public string $city = '';
    public string $courierComment = '';
    public string $entry = '';
    public string $entryCode = '';
    public string $flat = '';
    public string $flor = '';
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?int $pointId = null;
    public ?int $type = null;


    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [['id', 'userId', 'pointId', 'type'], 'integer'],
                [['entry', 'flat', 'flor', 'address', 'city', 'courierComment', 'entryCode'], 'string'],
                [['latitude', 'longitude'], 'number'],
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