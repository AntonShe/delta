<?php

namespace common\models\product;

use common\models\AbstractDecorator;
use common\models\DefaultFunctions;

class ProductBigCardDecorator extends AbstractDecorator
{
    public function getVoteCountText(): string
    {
        $functions = DefaultFunctions::getInstance();
        return $this->entity->voteCount . $functions->wordEnding(
            $this->entity->voteCount,
            ' оценка',
            ' оценки',
            ' оценок'
        );
    }

    public function getLanguages(): string
    {
        return $this->entity->languages ?
            implode(', ', array_column($this->entity->languages, 'name')) :
            '';
    }

    public function getCourse(): string
    {
        return $this->entity->course['name'] ?? '';
    }

    public function getCurrentGenre(): array
    {
        return $this->entity->genres ? end($this->entity->genres) : [];
    }

    public function getAges(): string
    {
        return $this->entity->ages ?
            implode(', ', array_column($this->entity->ages, 'name')) :
            '';
    }

    public function getPreparedAnnotation()
    {
        return str_replace('href="', 'class="link description__text-link" rel="nofollow" href="', $this->entity->annotation);
    }
}