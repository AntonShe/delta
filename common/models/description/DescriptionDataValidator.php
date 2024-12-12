<?php

namespace common\models\description;

use common\models\AbstractDataValidator;

class DescriptionDataValidator extends AbstractDataValidator
{
    public ?int $id = null;
    public $image = null;
    public string $main_text = '';
    public string $text = '';
    public string $name = '';
    public string $link = '';

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['main_text', 'text', 'link', 'name'], 'trim'],
            [['main_text', 'text', 'link', 'name'], 'string'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            ['is_del', 'boolean'],
            ['id', 'safe']
        ]);
    }
}