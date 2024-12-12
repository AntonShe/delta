<?php

namespace common\models\favorite;

use common\models\AbstractParamsValidator;

class FavoriteParamsValidator extends AbstractParamsValidator
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            []
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