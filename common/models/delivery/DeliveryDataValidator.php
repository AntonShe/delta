<?php

namespace common\models\delivery;

use common\models\AbstractDataValidator;

class DeliveryDataValidator extends AbstractDataValidator
{
    public ?string $address = '';
    public ?int $idPoint = null;
    public ?float $latitude = null;
    public ?float $longitude = null;

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                ['idPoint', 'integer'],
                [['latitude', 'longitude'], 'number'],
                [['address'], 'string'],
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