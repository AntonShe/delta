<?php

namespace common\models\person;

use common\models\AbstractDataValidator;

class PersonDataValidator extends AbstractDataValidator
{
    public ?string $nameFull= null;
    public ?string $nameFullRu = null;
    public ?string $alternativeName = null;
    public ?string $description= null;
    public ?string $seoTitle= null;
    public ?string $seoMetaKeywords= null;
    public ?string $seoMetaDescription = null;

    public ?int $active = null;
    public ?array $file = null;
    public ?int $id = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['active'], 'integer'],
            [['id'], 'integer'],
            [['file'], 'file', 'extensions' => ['png', 'jpg', 'jpeg'], 'maxSize' => 1024*1024*5, 'skipOnEmpty' => true],
            [['nameFull', 'nameFullRu', 'alternativeName', 'description', 'seoTitle',
                'seoMetaKeywords', 'seoMetaDescription'], 'string'],
        ]);
    }
}