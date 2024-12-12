<?php

namespace common\models\product;

use common\models\AbstractDataValidator;

class ProductDataValidator extends AbstractDataValidator
{
    public ?string $title = null;
    public ?string $authors = null;
    public ?int $publishingHouseId = null;
    public ?int $publishingYear = null;
    public ?int $volumesCount = null;
    public ?int $pagesNumber = null;
    public ?string $color = null;
    public ?string $pageMaterial = null;
    public ?array $ages = null;
    public ?string $size = null;
    public ?int $weight = null;
    public ?string $annotation = null;
    public ?string $shortAnnotation = null;
    public ?array $languages = null;
    public ?array $genres = null;
    public ?int $courseId = null;
    public ?array $levels = null;
    public ?bool $active = null;
    public ?bool $isNew = null;
    public ?bool $isPopular = null;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['title', 'color', 'pageMaterial', 'annotation', 'shortAnnotation', 'authors', 'size'], 'string'],
            [['publishingHouseId', 'publishingYear', 'volumesCount',
                'pagesNumber', 'weight', 'courseId'], 'integer', 'min' => 1],
            [['active', 'isNew', 'isPopular'], 'boolean'],
            [['languages', 'genres', 'levels', 'ages'], 'validateIntArray'],
        ]);
    }
}