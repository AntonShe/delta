<?php

namespace common\models\course;

use common\models\AbstractParamsValidator;

class CourseParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;
    public array $ids = [];

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['id', 'integer'],
            ['ids', 'validateIntArray'],
        ]);
    }
}