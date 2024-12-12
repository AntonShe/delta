<?php

namespace common\models\rsb_transaction;

use common\models\AbstractDataValidator;

class RsbTransactionDataValidator extends AbstractDataValidator
{
    public ?string $transactionId = null;
    public ?int $orderNumber = null;
    public ?int $userId = null;
    public ?int $uniqId = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['transactionId', 'uniqId', 'clientIpAddr'], 'string'],
            [['orderNumber', 'userId', 'status', 'type'], 'integer', 'min' => 1],
        ]);
    }
}