<?php

namespace common\models\genre;

use common\models\AbstractDTO;
use common\models\product\ProductEntity;

class GenreDTO extends AbstractDTO
{
    public function getUrl(): string
    {
        return "/catalog/{$this->entity->id}";
    }

    public function getSmallDescription(): string
    {
        return '';
    }

    public function getImages(): array
    {
        $entity = new ProductEntity();
        return $entity::find()
            ->select('products.cover')
            ->where('price IS NOT NULL AND price > 0 AND cover IS NOT NULL')
            ->limit(4)
            ->join(
                'JOIN',
                'product_genres',
                "product_genres.genre_id IN ({$this->entity->id}) AND product_genres.product_id = products.id")
            ->asArray()
            ->column() ?? [];
    }

    public function getPreparedDescription()
    {
        return str_replace('href="', 'class="link description__text-link" rel="nofollow" href="', $this->entity->description);
    }
}