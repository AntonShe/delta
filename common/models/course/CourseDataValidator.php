<?php

namespace common\models\course;

use common\models\AbstractDataValidator;

class CourseDataValidator extends AbstractDataValidator
{
    public function rules(): array
    {
        return array_merge(parent::rules(), []);
    }
}