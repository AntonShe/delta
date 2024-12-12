<?php

/** @var yii\web\View $this */
/** @var array $similarProducts */
/** @var ProductBigCardDecorator $data */

use common\models\product\ProductBigCardDecorator;
use frontend\widgets\breadcrumbs\BreadcrumbsWidget;
use frontend\widgets\favorite\FavoriteWidget;
use frontend\widgets\add_to_cart\AddToCartWidget;

$this->title = $data->title;

$genresArr = $data->genres;
$genres = '';
for ($i = 0; $i < count($genresArr); $i++) {
    $genres .= $genresArr[$i]['name'];
    if ($i !== count($genresArr) - 1) {
        $genres .= '/';
    }
}
?>
<article>
    <section class="section container big-card js-big-card" itemtype="http://schema.org/Product" itemscope
             data-product-id="<?= $data->id ?>"
             data-product-name="<?= $data->title ?>"
             data-product-brand="<?= $data->publishingHouse ?>"
             data-product-oldPrice="<?= $data->oldPrice ?>"
             data-product-price="<?= $data->price ?>"
             data-product-genres="<?= $genres ?>"
    >
        <?= BreadcrumbsWidget::widget([
            'template' => BreadcrumbsWidget::BREADCRUMBS_TYPE_PRODUCT,
            'genre' => $data->currentGenre,
            'title' => $data->title,
        ]) ?>

        <meta itemprop="sku" content="<?= $data->id ?>" />
        <meta itemprop="name" content="<?= $data->title ?>" />
        <link itemprop="image" href="<?= $data->cover ?>" />
        <meta itemprop="description" content="<?= strip_tags($data->getPreparedAnnotation()) ?>" />

        <div class="page-top__title-wrap flex">
            <a href="/catalog/<?= $data->currentGenre['id'] ?>" class="page-top__title-link button-back"></a>
            <h1 class="page-top__title"><?= $data->title ?></h1>
        </div>

        <div class="big-card__wrapper">
            <div class="big-card__modal js-big-card-mobile hidden">
                <div class="big-card__modal-content js-big-card-mobile-content">
                    <div class="big-card__modal-body">
                        <div class="big-card__modal-swiper js-big-card__modal-swiper swiper">
                            <div class="swiper-wrapper">
                                <div class="big-card__modal-swiper-slide swiper-slide">
                                    <img class="big-card__img big-card__img_modal <?= ($data->active && $data->quantity) ? '' : 'big-card__img--opacity'; ?>" src="<?= $data->cover ?: '/img/empty.png' ?>" alt="<?= $data->title ?>">
                                </div>
                                <?php foreach($data->images as $slide): ?>
                                <div class="big-card__modal-swiper-slide swiper-slide">
                                    <img class="big-card__img big-card__img_modal <?= ($data->active && $data->quantity) ? '' : 'big-card__img--opacity'; ?>" src="<?= $slide['url'] ?>" alt=""/>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php if($data->images): ?>
                        <div class="big-card__modal-button big-card__modal-button-prev js-big-card__modal-button-prev"></div>
                        <div class="big-card__modal-button big-card__modal-button-next js-big-card__modal-button-next"></div>
                        <?php endif; ?>
                    </div>                        
                </div>
                <div class="big-card__modal-close js-big-card-close">Закрыть</div>
                <div class="big-card__modal-pagination"></div>
            </div>
            <div class="big-card__cover-container <?= $data->images && $data->cover ? 'big-card__cover-container--swiper' : ''?>">
                <?php if ($data->images && $data->cover): ?>
                <div class="big-card__swiper-container">
                    <div class="big-card__swiper js-big-card__swiper swiper <?= count($data->images) > 2 ? 'big-card__swiper--navigation' : ''?>">
                        <div class="swiper-wrapper">
                            <?php foreach($data->images as $key => $slide): ?>
                            <div class="big-card__swiper-slide swiper-slide">
                                <img class="big-card__swiper-img js-big-card__swiper-img <?= ($data->active && $data->quantity) ? '' : 'big-card__img--opacity'; ?>" src="<?= $slide['url'] ?>" alt="" data-swiper-to="<?= $key + 1 ?>"/>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if(count($data->images) > 2): ?>
                    <div class="big-card__navigation-button">
                        <div class="big-card__swiper-button big-card__swiper-prev js-big-card__swiper-prev"></div>
                        <div class="big-card__swiper-button big-card__swiper-next js-big-card__swiper-next"></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="big-card__img-wrap">
                    <img class="js-big-card-img big-card__img <?= ($data->active && $data->quantity) ? '' : 'big-card__img--opacity'; ?>" src="<?= $data->cover ?: '/img/empty.png' ?>" alt="<?= $data->title ?>">
                </div>
            </div>
            <div class="big-card__top">
                <div class="big-card__top-wrap flex" itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating" itemscope>
                    <meta itemprop="reviewCount" content="356" />
                    <meta itemprop="ratingValue" content="4.8" />

                    <?= $this->render('@app/views/rating/big-card-default.php', [
                        'value' => $data->rating,
                        'text' => $data->voteCountText
                    ]) ?>
                    <?php if($data->languages): ?>
                        <div class="big-card__top-lang"><?= $data->languages ?></div>
                    <?php endif; ?>
                </div>
                <?php if($data->shortAnnotation): ?>
                    <p class="big-card__top-descr">
                        <?= $data->shortAnnotation ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="big-card__bottom">
                <?php if($data->persons): ?>
                    <div class="big-card__bottom-item flex">
                        <div class="big-card__item-left">Автор</div>
                        <div class="big-card__item-right">
                            <?= $this->render('@app/views/person/persons.php', ['persons' => $data->persons, 'isBigCard' => true]) ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if($data->publishingHouse): ?>
                    <div class="big-card__bottom-item flex" itemprop="brand" itemtype="http://schema.org/Thing" itemscope>
                        <meta itemprop="name" content="<?= $data->publishingHouse ?>" />
                        <div class="big-card__item-left">Издательство</div>
                        <div class="big-card__item-right"><a class="big-card__person-link" href="/publisher/<?= $data->publishingHouseId ?>"><?= $data->publishingHouse ?></a></div>
                    </div>
                <?php endif; ?>
                <?php if($data->course): ?>
                    <div class="big-card__bottom-item flex">
                        <div class="big-card__item-left">Серия / Курс</div>
                        <div class="big-card__item-right"><?= $data->course ?></div>
                    </div>
                <?php endif; ?>
                <div class="big-card__bottom-item flex">
                    <div class="big-card__item-left">ISBN</div>
                    <div class="big-card__item-right"><?= $data->isbn ?></div>
                </div>
                <?php if($data->seriesId): ?>
                    <div class="big-card__bottom-item flex">
                        <div class="big-card__item-left">Серия</div>
                        <div class="big-card__item-right">
                            <a class="big-card__person-link" href="/series/<?= $data->seriesId ?>"><?= $data->seriesName ?></a>
                        </div>
                    </div>
                <?php endif; ?>                
                <?php if($data->pdf): ?>
                    <a href="<?= $data->pdf ?>" class="big-card__bottom-btn link button-red">Полистать книгу</a>
                <?php endif; ?>
            </div>
            <div class="big-card__main">
                <div class="big-card__main-top" itemprop="offers" itemtype="http://schema.org/Offer" itemscope>
                    <link itemprop="url" href="<?= $_SERVER['REQUEST_URI'] ?>" />
                    <meta itemprop="availability" content="<?= ($data->active && $data->quantity) ? 'https://schema.org/InStock' : 'http://schema.org/OutOfStock'; ?>" />
                    <meta itemprop="priceCurrency" content="RUB" />
                    <meta itemprop="itemCondition" content="https://schema.org/NewCondition" />
                    <meta itemprop="price" content="<?= preg_replace('/[^0-9]/', "", $data->price) ?>" />
                    <meta itemprop="priceValidUntil" content="2025-11-05" />
                    <div itemprop="seller" itemtype="http://schema.org/Organization" itemscope>
                        <meta itemprop="name" content="Дельтабук" />
                    </div>
                    
                    <div class="big-card__main-item big-card__main-states flex">
                        <?php if($data->active && $data->quantity): ?>
                            <div class="big-card__state big-card__state_color_available">В наличии</div>
                            <div class="big-card__label-discount">-20%</div>
                        <?php else: ?>
                            <div class="big-card__state big-card__state_color_not-available">Нет в наличии</div>
                        <?php endif; ?>
                        <?= FavoriteWidget::widget([
                            'id' => $data->id,
                            'additionalClasses' => ['big-card__button-like']
                        ]) ?>
                    </div>
                    <?php if($data->active && $data->quantity): ?>
                        <div class="big-card__main-item big-card__main-prices flex">
                            <div class="big-card__price-new"><?= $data->price ?></div>
                            <?php if ($data->Oldprice): ?>
                                <div class="big-card__price-old"><?= $data->oldPrice ?></div>
                            <?php endif; ?>
                        </div>
                        <?= AddToCartWidget::widget([
                            'id' => $data->id,
                            'title' => $data->title,
                            'brand' => $data->publishingHouse,
                            'oldPrice' => $data->oldPrice,
                            'price' => $data->price,
                            'genres' => $genres,
                            'additionalClasses' => ['card__main-item big-card__main-buttons flex'],
                            'maxQuantity' => $data->quantity,
                        ]) ?>
                    <?php endif; ?>
                    <div class="big-card__main-item big-card__main-share flex">
                        <?= $this->render('@app/views/share_btn/index.php', ['link' => true, 'color' => true]) ?>
                    </div>
                </div>
                <?= $this->render('@app/views/rating/big-card-interactive.php', ['productId' => $data->id]) ?>
            </div>
        </div>
    </section>

    <section class="section container description js-container">
        <h3 class="description__title">Описание</h3>
        <div class="description__text js-show-more-content">
            <?= $data->getPreparedAnnotation() ?>
        </div>
        <?php if($data->getPreparedAnnotation()): ?>
            <button class="description__btn button-more button-text js-show-more-button" data-text="Развернуть">
                Развернуть
            </button>
        <?php endif; ?>
    </section>

    <section class="section container сharacteristic">

        <h3 class="сharacteristic__title">Характеристики</h3>
        <div class="сharacteristic__wrapper flex">
            <div class="сharacteristic__left">
                <h4 class="сharacteristic__title-min">Основные</h4>
                <ul class="characteristic__list">
                    <li class="сharacteristic__item flex">
                        <span class="сharacteristic__item-left">Дата выпуска</span>
                        <span class="сharacteristic__item-right">
                            <?= $data->publishingYear ?>
                        </span>
                    </li>
                    <?php if($data->volumesCount): ?>
                        <li class="сharacteristic__item flex">
                            <span class="сharacteristic__item-left">Количество томов</span>
                            <span class="сharacteristic__item-right">
                                <?= $data->volumesCount ?>
                            </span>
                        </li>
                    <?php endif; ?>
                    <?php if($data->pagesNumber): ?>
                        <li class="сharacteristic__item flex">
                            <span class="сharacteristic__item-left">Количество страниц</span>
                            <span class="сharacteristic__item-right">
                                <?= $data->pagesNumber ?>
                            </span>
                        </li>
                    <?php endif; ?>
                    <?php if($data->ageCategory): ?>
                        <li class="сharacteristic__item flex">
                            <span class="сharacteristic__item-left">Возрастная категория</span>
                            <span class="сharacteristic__item-right">
                                <?= $data->ageCategory ?>
                            </span>
                        </li>
                    <?php endif; ?>
                    <?php if($data->id): ?>
                        <li class="сharacteristic__item flex">
                            <span class="сharacteristic__item-left">Артикул</span>
                            <span class="сharacteristic__item-right">
                                <?= $data->id ?>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="сharacteristic__right">
                <?php if($data->size || $data->color || $data->pageMaterial || $data->bindingMaterial || $data->weight): ?>
                    <h4 class="сharacteristic__title-min">Дополнительные</h4>
                    <ul class="characteristic__list">
                        <?php if($data->size): ?>
                            <li class="сharacteristic__item flex">
                                <span class="сharacteristic__item-left">Размер (мм)</span>
                                <span class="сharacteristic__item-right">
                                    <?= $data->size ?>
                                </span>
                            </li>
                        <?php endif; ?>
                        <?php if($data->color): ?>
                            <li class="сharacteristic__item flex">
                                <span class="сharacteristic__item-left">Цвет</span>
                                <span class="сharacteristic__item-right">
                                    <?= $data->color ?>
                                </span>
                            </li>
                        <?php endif; ?>
                        <?php if($data->pageMaterial): ?>
                            <li class="сharacteristic__item flex">
                                <span class="сharacteristic__item-left">Тип бумаги</span>
                                <span class="сharacteristic__item-right">
                                    <?= $data->pageMaterial ?>
                                </span>
                            </li>
                        <?php endif; ?>
                        <?php if($data->weight): ?>
                            <li class="сharacteristic__item flex">
                                <span class="сharacteristic__item-left">Вес (г)</span>
                                <span class="сharacteristic__item-right">
                                    <?= $data->weight ?>
                                </span>
                            </li>
                        <?php endif; ?>
                        <?php if($data->bindingMaterial): ?>
                            <li class="сharacteristic__item flex">
                                <span class="сharacteristic__item-left">Тип переплёта</span>
                                <span class="сharacteristic__item-right">
                                    <?= $data->bindingMaterial ?>
                                </span>
                            </li>
                        <?php endif; ?>                        
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        </div>
    </section>

    <section class="section container card-catalog js-card-catalog"
        data-productList-id="series"
        data-productList-name="Еще книги из серии"
    >
        <?php if(!empty($similarProducts)): ?>
            <?= $this->render('_similar-by-series', ['list' => $similarProducts]) ?>
        <?php endif; ?>
    </section>

    <section class="section container SEO-info">
        <p class="SEO-info__descr">
            Об ошибках и неточностях в описании товара <?= $data->title?> за авторством <?= $data->authorsSimple ?> Вы можете сообщить нам по почте shop@deltabook.ru
        </p>
    </section>
</article>

<div class="hidden" itemscope itemtype="http://schema.org/Organization">
	<span itemprop="name">ООО “Цунами Букс”</span>
	<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
		<span itemprop="streetAddress">1-я Мытищинская ул., 3, стр. 1</span>
		<span itemprop="postalCode">129626</span>
		<span itemprop="addressLocality">Москва, Россия</span>
	</div>
	<span itemprop="telephone">+7(495)780-00-98</span>,
	<span itemprop="email">shop@deltabook.ru</span>
</div>