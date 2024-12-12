<?php

namespace common\models\genre;

use common\models\AbstractDataValidator;

class GenreOnMainValidator extends AbstractDataValidator
{
    public ?int $genreId = null;
    public ?string $title = null;
    public ?string $subtitle = null;
    public ?string $text = null;
    public ?array $products = null;


    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['genreId', 'integer', 'min' => 1],
            [['title', 'subtitle', 'text'], 'string'],
            ['products', 'validateIntArray']
        ]);
    }
}