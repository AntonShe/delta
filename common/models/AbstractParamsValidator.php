<?php

namespace common\models;

class AbstractParamsValidator extends AbstractValidator
{
    public ?int $page = null;

    public function rules(): array
    {
        return [
            ['page', 'integer', 'min' => 1],
        ];
    }

    public function getIgnoringValues(): array
    {
        return [null, '', []];
    }
}