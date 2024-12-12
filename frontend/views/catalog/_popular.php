<?php
/** @var array $list */
?>
<div class="popular__wrapper">
    <div class="page-top__title-wrap flex">
        <a href="#" class="page-top__title-link button-back"></a>
        <h2 class="popular__title">
            Популярные книги
        </h2>
    </div>

    <div class="swiper popular__swiper">
        <div class="popular__swiper-button button-swiper button-swiper-prev"></div>
        <div class="popular__swiper-button button-swiper button-swiper-next"></div>
        <div class="popular__swiper-wrapper swiper-wrapper">
            <?php foreach ($list as $item): ?>
                <div class="popular__swiper-slide swiper-slide">
                    <?= $this->render('@app/views/product_cards/small', ['data' => $item]) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>