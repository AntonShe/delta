<?php
/** @var AbstractDecorator $data */
/** @var int $index */
/** @var string $list_id */
/** @var string $list_name */

use common\models\AbstractDecorator;
use frontend\widgets\favorite\FavoriteWidget;
use frontend\widgets\add_to_cart\AddToCartWidget;


$genresArr = $data->genres;
$genres = '';
for ($i = 0; $i < count($genresArr); $i++) {
    $genres .= $genresArr[$i]['name'];
    if ($i !== count($genresArr) - 1) {
        $genres .= '/';
    }
}
?>
<div class="mini-card flex-between js-mini-card"
     data-product-id="<?= $data->id ?>"
     data-product-name="<?= $data->title ?>"
     data-product-brand="<?= $data->publishingHouse ?>"
     data-product-price="<?= $data->price ?>"
     data-product-oldPrice="<?= $data->oldPrice ?>"
     data-product-genres="<?= $genres ?>"
     data-product-index="<?= $index ?>"
     data-product-list_id="<?= $list_id ?>"
     data-product-list_name="<?= $list_name ?>"
>
    <div class="mini-card__top">
        <a href="<?= $data->url ?>" class="mini-card__top-link js-mini-card-link">
            <img class="mini-card__img <?= ($data->active && $data->quantity) ? '' : 'mini-card__img--opacity'; ?>" src="<?= $data->cover ?: '/img/empty.png' ?>" alt="<?= $data->title ?>">
        </a>
        <?= FavoriteWidget::widget(['id' => $data->id]) ?>
    </div>
    <div class="mini-card__bottom flex-between">
        <div class="mini-card__text-wrapper">        
            <?php if ($data->active && $data->quantity): ?>
                <div class="mini-card__price flex">
                    <div class="mini-card__price-new"><?= $data->price ?></div>
                    <?php if ($data->Oldprice): ?>
                        <div class="mini-card__price-old"><?= $data->oldPrice ?></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="mini-card__notsale">Нет в наличии</div>
            <?php endif; ?>
            <a href="<?= $data->url ?>" class="mini-card__title link js-mini-card-link">
                <?= $data->title ?>
            </a>
            <?php if ($data->persons): ?>
                <div class="mini-card__person-wrap">
                    <?= $this->render('@app/views/person/persons.php', ['persons' => $data->persons, 'isBigCard' => false]) ?>
                </div>
            <?php endif; ?>
            <div class="mini-card__rating-wrapper">
                <?= $this->render('@app/views/rating/small-card-default.php', ['value' => $data->rating]) ?>
            </div>
        </div>
        <?php if ($data->active && $data->quantity): ?>
            <?= AddToCartWidget::widget([
                'id' => $data->id,
                'title' => $data->title,
                'brand' => $data->publishingHouse,
                'oldPrice' => $data->oldPrice,
                'price' => $data->price,
                'genres' => $genres,
                'list_id' => $list_id,
                'list_name' => $list_name,
                'additionalClasses' => ['mini-card__button-wrap'],
                'maxQuantity' => $data->quantity,
            ]) ?>
        <?php endif; ?>  
    </div>
</div>