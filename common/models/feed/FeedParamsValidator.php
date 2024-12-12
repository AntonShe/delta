<?php

namespace common\models\feed;

use common\models\AbstractParamsValidator;

class FeedParamsValidator extends AbstractParamsValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}