<?php

namespace common\models\feed;

use common\models\AbstractDataValidator;

class FeedDataValidator extends AbstractDataValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}