<?php

namespace common\models\genre;

use common\models\AbstractDataValidator;

class GenreDataValidator extends AbstractDataValidator
{
    public ?string $name = null;
    public ?int $sort = null;
    public ?string $level = null;
    public ?string $description = null;
    public ?int $parentId = null;
    public ?bool $onMain = null;
    public ?bool $popular = null;
    public ?bool $isCourse = null;
    public ?array $onMainInfo = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['name', 'sort', 'level', 'description'], 'string'],
            [['parentId', 'sort'], 'integer', 'min' => 1],
            [['onMain', 'popular', 'isCourse'], 'boolean'],
            ['onMainInfo', 'validateGenresOnMain']
        ]);
    }

    public function validateGenresOnMain($attribute, $params)
    {
        $entity = new GenreOnMainValidator();
        if (!$entity->load($this->$attribute, '') || !$entity->validate()) {
            $this->addError($attribute, 'Неверно заполнены поля onMainInfo');
        }
    }
}