<?php

namespace common\models\order;

use common\models\AbstractDataValidator;

class OrderDataValidator extends AbstractDataValidator
{
    public ?int $id = null;
    public array $products = [];
    public array $user = [];
    public ?array $subUser = [];
    public int $paymentType = 0;
    public ?string $managerComment = '';
    public ?string $deliveryDate = '';
    public ?string $possibleDeliveryDate = '';
    public array $delivery = [];
    public array $orderParams = [];
    public array $userData = [];

    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'id',
                        'paymentType'
                    ], 'integer'
                ],
                [['products', 'user', 'paymentType', 'delivery', 'subUser', 'orderParams', 'userData'], 'safe'],
                [['managerComment', 'deliveryDate', 'possibleDeliveryDate'], 'string'],
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