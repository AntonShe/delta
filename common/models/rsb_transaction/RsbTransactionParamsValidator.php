<?php

namespace common\models\rsb_transaction;

use common\models\AbstractParamsValidator;

class RsbTransactionParamsValidator extends AbstractParamsValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}