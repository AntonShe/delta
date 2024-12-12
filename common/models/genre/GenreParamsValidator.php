<?php

namespace common\models\genre;

use common\models\AbstractParamsValidator;

class GenreParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;
    public ?array $ids = null;
    public ?array $level = null;
    public ?bool $buildTree = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['buildTree', 'boolean'],
            ['id', 'integer'],
            [['ids', 'level'], 'validateIntArray'],
        ]);
    }
}