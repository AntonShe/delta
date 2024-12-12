<?php

namespace common\models\product;

use common\models\AbstractDecorator;

class ProductDecorator extends AbstractDecorator
{
    public function getAuthors(): string
    {
        return "<div class='mini-card__autor'>{$this->entity->authors}</div>";
    }

    public function getAuthorsSimple(): string
    {
        return $this->entity->authors;
    }

    public function getUrl(): string
    {
        return "/product/{$this->entity->id}";
    }

    public function getPrice(): string
    {
        return "{$this::priceFormat($this->entity->price)}&nbsp;₽";
    }

    public function getOldPrice(): string
    {
        return "{$this::priceFormat($this->entity->oldPrice)}&nbsp;₽";
    }

    private static function priceFormat(int $price): string
    {
        return number_format($price, 0, ',', '&nbsp;');
    }

    /**
     * @return void
     */
    public function getPersonsUrl(): void
    {
        foreach ($this->entity->persons as &$person) {
            $person['url'] = "/person/{$person['id']}";
        }
    }
}