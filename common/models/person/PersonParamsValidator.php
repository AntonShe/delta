<?php

namespace common\models\person;

use common\models\AbstractParamsValidator;

class PersonParamsValidator extends AbstractParamsValidator
{
    public ?int $id = null;
    public ?string $nameFull= null;
    public ?string $nameFullRu = null;
    public ?string $alternativeName = null;
    public ?string $description= null;
    public ?string $seoTitle= null;
    public ?string $seoMetaKeywords= null;
    public ?string $seoMetaDescription = null;
    public string $search= '';

    public ?bool $active = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['id',], 'integer', 'min' => 1],
            [['active'], 'boolean'],
            [['nameFull', 'nameFullRu', 'alternativeName', 'description', 'seoTitle',
                'seoMetaKeywords', 'seoMetaDescription', 'search'], 'string'],
        ]);
    }
}