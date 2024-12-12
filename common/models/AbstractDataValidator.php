<?php

namespace common\models;

class AbstractDataValidator extends AbstractValidator
{
    public function rules(): array
    {
        return [];
    }

    public function getIgnoringValues(): array
    {
        return [null];
    }
}