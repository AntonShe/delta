<?php

namespace common\models\publishing_house;

use common\models\AbstractDataValidator;

class PublishingHouseDataValidator extends AbstractDataValidator
{
    public ?string $description = null;
    public ?string $seoTitle= null;
    public ?string $seoMetaKeywords= null;
    public ?string $seoMetaDescription = null;
    public ?string $name = null;
    public ?array $file = null;
    public ?int $id = null;
    public ?bool $isActive = null;
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['description', 'name', 'seoTitle', 'seoMetaKeywords', 'seoMetaDescription'], 'string'],
            [['isActive'], 'boolean'],
            ['id', 'integer'],
            ['file', 'file', 'extensions' => ['png', 'jpg', 'jpeg'], 'maxSize' => 1024*1024*5, 'skipOnEmpty' => true],
        ]);
    }
}